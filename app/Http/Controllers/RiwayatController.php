<?php

namespace App\Http\Controllers;

use Closure;
use App\Models\BBM;
use App\Models\Pajak;
use App\Models\Asuransi;
use App\Models\CekFisik;
use App\Models\Kendaraan;
use App\Models\Peminjaman;
use App\Models\ServisRutin;
use Illuminate\Http\Request;
use App\Models\ServisInsidental;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User;

class RiwayatController extends Controller
{
    public function index()
    {
        return view('admin.riwayat.index');
    }

    private function buildDateSearch($query, $columns, $search)
    {
        foreach ($columns as $column) {
            // Check if search might be a date in various formats

            // Format: d (day only - 1 to 31)
            if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])$/', $search)) {
                $day = (int) $search;
                $query->orWhereRaw("DAY($column) = ?", [$day]);
            }

            // Format: m (month only - 1 to 12)
            if (preg_match('/^(0?[1-9]|1[0-2])$/', $search)) {
                $month = (int) $search;
                $query->orWhereRaw("MONTH($column) = ?", [$month]);
            }

            // Format: Y (year only - 4 digits)
            if (preg_match('/^(20\d{2})$/', $search)) {
                $year = (int) $search;
                $query->orWhereRaw("YEAR($column) = ?", [$year]);
            }

            // Format: d-m (day-month)
            if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])$/', $search)) {
                $parts = preg_split('/[\-\/]/', $search);
                $day = (int) $parts[0];
                $month = (int) $parts[1];
                $query->orWhereRaw("DAY($column) = ? AND MONTH($column) = ?", [$day, $month]);
            }

            // Format: m-Y (month-year)
            if (preg_match('/^(0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
                $parts = preg_split('/[\-\/]/', $search);
                $month = (int) $parts[0];
                $year = (int) $parts[1];
                $query->orWhereRaw("MONTH($column) = ? AND YEAR($column) = ?", [$month, $year]);
            }

            // Format: d-m-Y (day-month-year)
            if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
                $parts = preg_split('/[\-\/]/', $search);
                $day = (int) $parts[0];
                $month = (int) $parts[1];
                $year = (int) $parts[2];
                $query->orWhereRaw("DAY($column) = ? AND MONTH($column) = ? AND YEAR($column) = ?", [$day, $month, $year]);
            }

            // Default LIKE search
            $query->orWhere($column, 'like', "%$search%");
        }
    }

    private function applySplitSearch($query, $search, Closure $searchCallback)
    {
        $searchWords = preg_split('/\s+/', trim($search));

        $query->where(function ($q) use ($searchWords, $searchCallback) {
            foreach ($searchWords as $word) {
                $q->where(function ($q2) use ($word, $searchCallback) {
                    $searchCallback($q2, $word);
                });
            }
        });

        return $query;
    }

    /**
     * Helper function to build date search conditions
     * SEARCH UNTUK 1 KOLOM
     */
    // private function buildDateSearch($query, $column, $search)
    // {
    //     // Check if search might be a date in various formats
    //     // Format: d (day only - 1 to 31)
    //     if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])$/', $search)) {
    //         $day = (int) $search;
    //         $query->orWhereRaw("DAY($column) = ?", [$day]);
    //     }
        
    //     // Format: m (month only - 1 to 12)
    //     if (preg_match('/^(0?[1-9]|1[0-2])$/', $search)) {
    //         $month = (int) $search;
    //         $query->orWhereRaw("MONTH($column) = ?", [$month]);
    //     }
        
    //     // Format: Y (year only - 4 digits)
    //     if (preg_match('/^(20\d{2})$/', $search)) {
    //         $year = (int) $search;
    //         $query->orWhereRaw("YEAR($column) = ?", [$year]);
    //     }
        
    //     // Format: d-m (day-month)
    //     if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])$/', $search)) {
    //         $parts = preg_split('/[\-\/]/', $search);
    //         $day = (int) $parts[0];
    //         $month = (int) $parts[1];
    //         $query->orWhereRaw("DAY($column) = ? AND MONTH($column) = ?", [$day, $month]);
    //     }
        
    //     // Format: m-Y (month-year)
    //     if (preg_match('/^(0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
    //         $parts = preg_split('/[\-\/]/', $search);
    //         $month = (int) $parts[0];
    //         $year = (int) $parts[1];
    //         $query->orWhereRaw("MONTH($column) = ? AND YEAR($column) = ?", [$month, $year]);
    //     }
        
    //     // Format: d-m-Y (day-month-year)
    //     if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
    //         $parts = preg_split('/[\-\/]/', $search);
    //         $day = (int) $parts[0];
    //         $month = (int) $parts[1];
    //         $year = (int) $parts[2];
    //         $query->orWhereRaw("DAY($column) = ? AND MONTH($column) = ? AND YEAR($column) = ?", [$day, $month, $year]);
    //     }
        
    //     // Also try the default LIKE search for backward compatibility
    //     $query->orWhere($column, 'like', "%$search%");
    // }

    public function peminjaman(Request $request)
    {
        $search = $request->search;
        $query = Peminjaman::with(['user', 'kendaraan'])->orderBy('tgl_mulai', 'desc');

        // Search functionality
        // // search dari 1 kolom
        // if ($request->filled('search')) {
        //     $search = $request->search;
        //     $query->where(function ($q) use ($search) {
        //         $q->whereHas('user', function ($qUser) use ($search) {
        //             $qUser->where('name', 'like', "%$search%");
        //         })
        //         ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
        //             $qKendaraan->where('merk', 'like', "%$search%")
        //                     ->orWhere('tipe', 'like', "%$search%")
        //                     ->orWhere('plat_nomor', 'like', "%$search%");
        //         })
        //         ->orWhere('tujuan', 'like', "%$search%")
        //         // ->orWhere('tgl_mulai', 'like', "%$search%")
        //         // ->orWhere('tgl_selesai', 'like', "%$search%")
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_mulai', $search);
        //         })
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_selesai', $search);
        //         })
        //         ->orWhere('status_pinjam', 'like', "%$search%");
        //     });
        // }

        if ($request->filled('search')) {
            $searchWords = explode(' ', $request->search);
        
            $query->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->where(function ($q2) use ($word) {
                        $q2->whereHas('user', function ($qUser) use ($word) {
                                $qUser->where('name', 'like', "%$word%");
                            })
                            ->orWhereHas('kendaraan', function ($qKendaraan) use ($word) {
                                $qKendaraan->where('merk', 'like', "%$word%")
                                    ->orWhere('tipe', 'like', "%$word%")
                                    ->orWhere('plat_nomor', 'like', "%$word%");
                            })
                            ->orWhere('tujuan', 'like', "%$word%")
                            ->orWhere('status_pinjam', 'like', "%$word%")
                            ->orWhere(function ($q3) use ($word) {
                                $this->buildDateSearch($q3, ['tgl_mulai', 'tgl_selesai'], $word);
                            });
                    });
                }
            });
        }        

        // Paginate results
        $riwayatPeminjaman = $query->paginate(10);
        $kendaraan = Kendaraan::all(); // Ambil semua kendaraan untuk dropdown filter

        return view('admin.riwayat.peminjaman', compact('riwayatPeminjaman', 'kendaraan', 'search'));
    }

    public function detailPeminjaman($id)
    {
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])->findOrFail($id);

        return view('admin.riwayat.detail-peminjaman', compact('peminjaman'));
    }

    public function pajak(Request $request)
    {
        $search = $request->search;
        // Ambil data pajak dengan relasi kendaraan dan user (admin yang input)
        $query = Pajak::with(['kendaraan', 'user'])
            ->orderBy('tgl_bayar', 'desc')
            ->orderBy('tgl_jatuh_tempo', 'desc');

        // Jika ada pencarian berdasarkan plat, merek kendaraan, tipe, atau nama admin
        // // search 1 kolom
        // if ($request->filled('search')) {
        //     $search = $request->search;
        //     $query->where(function ($q) use ($search) {
        //         $q->whereHas('kendaraan', function ($qKendaraan) use ($search) {
        //             $qKendaraan->where('plat_nomor', 'like', "%$search%")
        //                     ->orWhere('merk', 'like', "%$search%")
        //                     ->orWhere('tipe', 'like', "%$search%");
        //         })
        //         ->orWhereHas('user', function ($qUser) use ($search) {
        //             $qUser->where('name', 'like', "%$search%");
        //         })
        //         // ->orWhere('tgl_bayar', 'like', "%$search%")
        //         // ->orWhere('tgl_jatuh_tempo', 'like', "%$search%");
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_bayar', $search);
        //         })
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_jatuh_tempo', $search);
        //         });
        //     });
        // }

        if ($request->filled('search')) {
            $searchWords = explode(' ', $search);
    
            $query->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->where(function ($q2) use ($word) {
                        $q2->whereHas('kendaraan', function ($qKendaraan) use ($word) {
                                $qKendaraan->where('plat_nomor', 'like', "%$word%")
                                    ->orWhere('merk', 'like', "%$word%")
                                    ->orWhere('tipe', 'like', "%$word%");
                            })
                            ->orWhereHas('user', function ($qUser) use ($word) {
                                $qUser->where('name', 'like', "%$word%");
                            })
                            ->orWhere(function ($q3) use ($word) {
                                $this->buildDateSearch($q3, ['tgl_bayar', 'tgl_jatuh_tempo'], $word);
                            });
                    });
                }
            });
        }

        // Paginate hasilnya
        $riwayatPajak = $query->paginate(10);

        return view('admin.riwayat.pajak', compact('riwayatPajak', 'search'));
    }

    public function detailPajak($id)
    {
        $pajak = Pajak::with(['kendaraan', 'user']) 
            ->where('id_pajak', $id)
            ->firstOrFail();
    
        $tglJatuhTempoTahunDepan = \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo)->addYear();
        $pajak->tgl_jatuh_tempo_tahun_depan = $tglJatuhTempoTahunDepan;
    
        return view('admin.riwayat.detail-pajak', compact('pajak'));
    }

    public function asuransi(Request $request)
    {
        $search = $request->search;
        $query = Asuransi::with(['kendaraan', 'user'])
            ->orderBy('tgl_bayar', 'desc');

        // // search 1 kolom
        // if ($request->has('search')) {
        //     $search = $request->search;
        //     $query->where(function ($q) use ($search) {
        //         $q->whereHas('kendaraan', function ($q) use ($search) {
        //             $q->where('merk', 'like', "%$search%")
        //             ->orWhere('tipe', 'like', "%$search%")
        //             ->orWhere('plat_nomor', 'like', "%$search%");
        //         })
        //         ->orWhereHas('user', function ($q) use ($search) {
        //             $q->where('name', 'like', "%$search%");
        //         })
        //         ->orWhere('tahun', 'like', "%$search%")
        //         // ->orWhere('tgl_bayar', 'like', "%$search%")
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_bayar', $search);
        //         })
        //         ->orWhere('polis', 'like', "%$search%")
        //         // ->orWhere('tgl_perlindungan_awal', 'like', "%$search%")
        //         // ->orWhere('tgl_perlindungan_akhir', 'like', "%$search%")
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_perlindungan_awal', $search);
        //         })
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_perlindungan_akhir', $search);
        //         })
        //         ->orWhere('nominal', 'like', "%$search%")
        //         ->orWhere('biaya_asuransi_lain', 'like', "%$search%");
        //     });
        // }

        if ($request->filled('search')) {
            $searchWords = preg_split('/\s+/', trim($search));
    
            $query->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->where(function ($q2) use ($word) {
                        $q2->whereHas('kendaraan', function ($qKendaraan) use ($word) {
                                $qKendaraan->where('merk', 'like', "%$word%")
                                    ->orWhere('tipe', 'like', "%$word%")
                                    ->orWhere('plat_nomor', 'like', "%$word%");
                            })
                            ->orWhereHas('user', function ($qUser) use ($word) {
                                $qUser->where('name', 'like', "%$word%");
                            })
                            ->orWhere('tahun', 'like', "%$word%")
                            ->orWhere(function ($q3) use ($word) {
                                $this->buildDateSearch($q3, ['tgl_bayar', 'tgl_perlindungan_awal', 'tgl_perlindungan_akhir'], $word);
                            })
                            ->orWhere('polis', 'like', "%$word%")
                            ->orWhere('nominal', 'like', "%$word%")
                            ->orWhere('biaya_asuransi_lain', 'like', "%$word%");
                    });
                }
            });
        }

        $riwayatAsuransi = $query->paginate(10);

        return view('admin.riwayat.asuransi', compact('riwayatAsuransi', 'search'));
    }

    public function detailAsuransi($id) 
    {
        $asuransi = Asuransi::with(['kendaraan', 'user']) 
            ->where('id_asuransi', $id)
            ->firstOrFail();
        
        $previousAsuransi = Asuransi::where('id_kendaraan', $asuransi->id_kendaraan)
            ->where('id_asuransi', '<', $id) 
            ->orderBy('id_asuransi', 'desc') 
            ->first();
        
        if ($previousAsuransi) {
            $asuransi->tgl_jatuh_tempo = $previousAsuransi->tgl_perlindungan_akhir;
        } else {
            $asuransi->tgl_jatuh_tempo = null;
        }

        return view('admin.riwayat.detail-asuransi', compact('asuransi'));
    }

    public function servisRutin(Request $request)
    {
        $search = $request->search;
        $query = ServisRutin::with(['kendaraan', 'user'])
            ->orderBy('tgl_servis_real', 'desc');

        // // search 1 kolom
        // if ($request->has('search')) {
        //     $search = $request->search;
        //     $query->where(function ($q) use ($search) {
        //         $q->whereHas('kendaraan', function ($q) use ($search) {
        //             $q->where('merk', 'like', "%$search%")
        //                 ->orWhere('tipe', 'like', "%$search%")
        //                 ->orWhere('plat_nomor', 'like', "%$search%");
        //         })
        //         ->orWhereHas('user', function ($q) use ($search) {
        //             $q->where('name', 'like', "%$search%");
        //         })
        //         ->orWhere('lokasi', 'like', "%$search%")
        //         ->orWhere('harga', 'like', "%$search%")
        //         ->orWhere('kilometer', 'like', "%$search%")
        //         // ->orWhere('tgl_servis_real', 'like', "%$search%")
        //         // ->orWhere('tgl_servis_selanjutnya', 'like', "%$search%");
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_servis_real', $search);
        //         });
        //         // ->orWhere(function ($q) use ($search) {
        //         //     $this->buildDateSearch($q, 'tgl_servis_selanjutnya', $search);
        //         // });
        //     });
        // }

        if ($request->filled('search')) {
            $searchWords = preg_split('/\s+/', trim($search));
    
            $query->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->where(function ($q2) use ($word) {
                        $q2->whereHas('kendaraan', function ($qKendaraan) use ($word) {
                                $qKendaraan->where('merk', 'like', "%$word%")
                                    ->orWhere('tipe', 'like', "%$word%")
                                    ->orWhere('plat_nomor', 'like', "%$word%");
                            })
                            ->orWhereHas('user', function ($qUser) use ($word) {
                                $qUser->where('name', 'like', "%$word%");
                            })
                            ->orWhere('lokasi', 'like', "%$word%")
                            ->orWhere('harga', 'like', "%$word%")
                            ->orWhere('kilometer', 'like', "%$word%")
                            ->orWhere(function ($q3) use ($word) {
                                $this->buildDateSearch($q3, ['tgl_servis_real'], $word);
                            });
                    });
                }
            });
        }

        $riwayatServis = $query->paginate(10);

        return view('admin.riwayat.servis-rutin', compact('riwayatServis', 'search'));
    }

    public function detailServisRutin($id)
    {
        $servis = ServisRutin::with(['kendaraan'])
            ->findOrFail($id);

        return view('admin.riwayat.detail-servis-rutin', compact('servis'));
    }

    public function servisInsidental(Request $request)
    {
        $search = $request->search;
        $query = ServisInsidental::with(['kendaraan', 'user'])
            ->orderBy('tgl_servis', 'desc');

        // // search 1 kolom
        // if ($request->has('search')) {
        //     $search = $request->search;
        //     $query->where(function ($q) use ($search) {
        //         $q->whereHas('kendaraan', function ($q) use ($search) {
        //             $q->where('merk', 'like', "%$search%")
        //                 ->orWhere('tipe', 'like', "%$search%")
        //                 ->orWhere('plat_nomor', 'like', "%$search%");
        //         })
        //         ->orWhereHas('user', function ($q) use ($search) {
        //             $q->where('name', 'like', "%$search%");
        //         })
        //         ->orWhere('lokasi', 'like', "%$search%")
        //         ->orWhere('harga', 'like', "%$search%")
        //         // ->orWhere('tgl_servis', 'like', "%$search%");
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_servis', $search);
        //         });
        //     });
        // }

        if ($request->filled('search')) {
            $searchWords = preg_split('/\s+/', trim($search));
    
            $query->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->where(function ($q2) use ($word) {
                        $q2->whereHas('kendaraan', function ($qKendaraan) use ($word) {
                                $qKendaraan->where('merk', 'like', "%$word%")
                                    ->orWhere('tipe', 'like', "%$word%")
                                    ->orWhere('plat_nomor', 'like', "%$word%");
                            })
                            ->orWhereHas('user', function ($qUser) use ($word) {
                                $qUser->where('name', 'like', "%$word%");
                            })
                            ->orWhere('lokasi', 'like', "%$word%")
                            ->orWhere('harga', 'like', "%$word%")
                            ->orWhere(function ($q3) use ($word) {
                                $this->buildDateSearch($q3, ['tgl_servis'], $word);
                            });
                    });
                }
            });
        }

        $riwayatServis = $query->paginate(10);

        return view('admin.riwayat.servis-insidental', compact('riwayatServis', 'search'));
    }

    public function detailServisInsidental($id)
    {
        $servis = ServisInsidental::with(['kendaraan'])
            ->findOrFail($id);

        return view('admin.riwayat.detail-servis-insidental', compact('servis'));
    }

    public function pengisianBBM(Request $request)
    {
        // Get the filter inputs from the request
        $kendaraan = $request->get('kendaraan');
        $pengguna = $request->get('pengguna');
        $tgl_awal = $request->get('tgl_awal');
        $tgl_akhir = $request->get('tgl_akhir');

        // Build the query
        $query = BBM::query();

        if ($kendaraan) {
            $query->whereHas('kendaraan', function($q) use ($kendaraan) {
                $q->where('plat_nomor', $kendaraan);
            });
        }

        if ($pengguna) {
            $query->whereHas('user', function($q) use ($pengguna) {
                $q->where('id', $pengguna);
            });
        }        

        if ($tgl_awal) {
            $query->whereDate('tgl_isi', '>=', $tgl_awal);
        }

        if ($tgl_akhir) {
            $query->whereDate('tgl_isi', '<=', $tgl_akhir);
        }

        $totalTransaksi = $query->sum('nominal');

        // Get the filtered data with pagination
        $riwayatBBM = $query->with(['kendaraan', 'user'])->paginate(10);

        // Get the list of vehicles and users for the filter
        $kendaraanList = Kendaraan::all();
        $penggunaList = User::all();

        return view('admin.riwayat.pengisian-bbm', [
            'riwayatBBM' => $riwayatBBM,
            'totalTransaksi' => $totalTransaksi,
            'kendaraan' => $kendaraanList,
            'penggunas' => $penggunaList,
            'filterParams' => [
                'kendaraan' => $kendaraan,
                'pengguna' => $pengguna,
                'tgl_awal' => $tgl_awal,
                'tgl_akhir' => $tgl_akhir
            ]
        ]);
    }


    public function detailPengisianBBM($id)
    {

        $bbm = BBM::with(['kendaraan'])->findOrFail($id);
        
        // Ambil semua parameter filter dari request
        $filterParams = [
            'kendaraan' => request()->query('kendaraan'),
            'pengguna' => request()->query('pengguna'),
            'tgl_awal' => request()->query('tgl_awal'),
            'tgl_akhir' => request()->query('tgl_akhir'),
            'page' => request()->query('page', 1)
        ];

        return view('admin.riwayat.detail-pengisian-bbm', compact('bbm', 'filterParams'));
    }

    public function cekFisik(Request $request)
    {
        $search = $request->search;
        $query = CekFisik::with(['kendaraan', 'user'])
            ->orderBy('tgl_cek_fisik', 'desc')
            ->orderBy('id_cek_fisik', 'desc');

        // // search 1 kolom aja
        // if ($request->has('search')) {
        //     $search = $request->search;
        //     $query->where(function ($q) use ($search) {
        //         $q->whereHas('kendaraan', function ($q) use ($search) {
        //             $q->where('merk', 'like', "%$search%")
        //                 ->orWhere('tipe', 'like', "%$search%")
        //                 ->orWhere('plat_nomor', 'like', "%$search%");
        //         })
        //         ->orWhereHas('user', function ($q) use ($search) {
        //             $q->where('name', 'like', "%$search%");
        //         })
        //         // ->orWhere('tgl_cek_fisik', 'like', "%$search%");
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_cek_fisik', $search);
        //         });
        //     });
        // }

        if ($request->filled('search')) {
            $searchWords = preg_split('/\s+/', trim($search));
    
            $query->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->where(function ($q2) use ($word) {
                        $q2->whereHas('kendaraan', function ($qKendaraan) use ($word) {
                            $qKendaraan->where('merk', 'like', "%$word%")
                                ->orWhere('tipe', 'like', "%$word%")
                                ->orWhere('plat_nomor', 'like', "%$word%");
                        })
                        ->orWhereHas('user', function ($qUser) use ($word) {
                            $qUser->where('name', 'like', "%$word%");
                        })
                        ->orWhere('kondisi_keseluruhan', 'like', "%$word%")
                        ->orWhere('tgl_cek_fisik', 'like', "%$word%");
                    });
                }
            });
        }

        $riwayatCekFisik = $query->paginate(10);

        return view('admin.riwayat.cek-fisik', compact('riwayatCekFisik', 'search'));
    }

    public function detailCekFisik($id)
    {
        $cekFisik = CekFisik::with(['kendaraan'])
            ->findOrFail($id);
    
        return view('admin.riwayat.detail-cek-fisik', compact('cekFisik'));
    }

}
