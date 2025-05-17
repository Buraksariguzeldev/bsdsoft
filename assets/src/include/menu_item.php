<?php
if ($kullanici_adi) {

    if ($kullanici_adi) {
        $menu_items['site_haritasi'] = [
            'url' => 'siteharitasi.php',
            'icon' => 'bi bi-diagram-3',
            'text' => 'Site Haritasi'
        ];
    }

    $menu_items['Ürunler'] = [
        'url' => '#',
        'icon' => 'bi bi-box-seam',
        'text' => 'Ürunler',
        'submenu' => [
            'Urun Duzenle' => [
                'url' => 'urunler/urun_ekle.php',
                'icon' => 'bi bi-plus-circle',
                'text' => 'Ürun ekle'
            ],
            'Ürun listesi' => [
                'url' => 'urunler/urun_listesi.php',
                'icon' => 'bi bi-list-ul',
                'text' => 'urun listesi'
            ]
        ]
    ];

    $menu_items['Ürun Grubu'] = [
        'url' => '#',
        'icon' => 'bi bi-grid-3x3-gap',
        'text' => 'Ürun Grubu',
        'submenu' => [
            'Urun Grubu Ekle' => [
                'url' => 'urun_gruplari/urun_grubu_ekle.php',
                'icon' => 'bi bi-plus-circle',
                'text' => 'Ürun Grubu ekle'
            ],
            'Ürun Grubu listesi' => [
                'url' => 'urun_gruplari/urun_grubu_listesi.php',
                'icon' => 'bi bi-list-ul',
                'text' => 'urun Grubu listesi'
            ]
        ]
    ];

    $menu_items['Marka'] = [
        'url' => '#',
        'icon' => 'bi bi-r-circle',
        'text' => 'Marka',
        'submenu' => [
            'Marka Ekle' => [
                'url' => 'marka/marka_ekle.php',
                'icon' => 'bi bi-plus-circle',
                'text' => 'Marka ekle'
            ],
            'Marka' => [
                'url' => 'marka/marka_listesi.php',
                'icon' => 'bi bi-list-ul',
                'text' => 'Marka listesi'
            ]
        ]
    ];

    $menu_items['musteriler'] = [
        'url' => '#',
        'icon' => 'bi bi-people',
        'text' => 'Musteriler',
        'submenu' => [
            'Musteri düzenle' => [
                'url' => 'musteriler/musteri_ekle.php',
                'icon' => 'bi bi-person-plus',
                'text' => 'Müsteri ekle'
            ],
            'Musteri görüntüle' => [
                'url' => 'musteriler/musteriler.php',
                'icon' => 'bi bi-person-lines-fill',
                'text' => 'Musteri listesi'
            ]
        ]
    ];

    $menu_items['Kasalar'] = [
        'url' => '#',
        'icon' => 'bi bi-bank',
        'text' => 'Kasalar',
        'submenu' => [
            'Kasa görüntüle' => [
                'url' => 'kasa_yonetim/kasa_listesi.php',
                'icon' => 'bi bi-building',
                'text' => 'Kasa Görüntüleme'
            ]
        ]
    ];

    $menu_items['Satis Yönetimi'] = [
        'url' => '#',
        'icon' => 'bi bi-cart-check',
        'text' => 'Satis Yönetimi',
        'submenu' => [
            'Satis Ekrani' => [
                'url' => 'satis_yonetimi/satis_paneli.php',
                'icon' => 'bi bi-cash-coin',
                'text' => 'Satis Ekrani'
            ],
            'Satis Listesi' => [
                'url' => 'satislar/satis_listesi.php',
                'icon' => 'bi bi-list-check',
                'text' => 'Satis Listesi'
            ],
            'Satis Takip' => [
                'url' => 'satislar/satis_takip.php',
                'icon' => 'bi bi-clipboard-data',
                'text' => 'Satis Takip'
            ]
        ]
    ];

    $menu_items['Raporlar'] = [
        'url' => '#',
        'icon' => 'bi bi-pie-chart-fill',
        'text' => 'Raporlar',
        'submenu' => [
            'Satis Raporu' => [
                'url' => 'raporlar/satis_raporu.php',
                'icon' => 'bi bi-graph-up',
                'text' => 'Satis Raporu'
            ],
            'Ürün Raporu' => [
                'url' => 'raporlar/urun_raporlari.php',
                'icon' => 'bi bi-bar-chart-line',
                'text' => 'Ürün Raporu'
            ]
        ]
    ];

    $menu_items['Güncelleme'] = [
        'url' => '#',
        'icon' => 'bi bi-arrow-clockwise',
        'text' => 'Güncelleme',
        'submenu' => [
            'Fiyat değişikliği' => [
                'url' => 'update/hizli_fiyat_guncelle.php',
                'icon' => 'bi bi-tag',
                'text' => 'Fiyat Değişikliği'
            ],
            'Resim Düzenle' => [
                'url' => 'update/görsel_yonetimi.php',
                'icon' => 'bi bi-image',
                'text' => 'Resim düzenle'
            ],
            'Hizli grup' => [
                'url' => 'update/urun_navigasyon_guncelle.php',
                'icon' => 'bi bi-layers',
                'text' => 'Hizli Gruplandir'
            ]
        ]
    ];

    $menu_items['modules'] = [
        'url' => '#',
        'icon' => 'bi bi-columns-gap',
        'text' => 'Modules',
        'submenu' => [
            'Modules php' => [
                'url' => 'modules/modules.php',
                'icon' => 'bi bi-gear',
                'text' => 'Modules'
            ],
            'Data' => [
                'url' => 'modules/data/data.php',
                'icon' => 'bi bi-hdd-network',
                'text' => 'Data'
            ]
        ]
    ];
}