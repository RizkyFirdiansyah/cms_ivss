<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        <?= htmlspecialchars($page_title) ?>
    </title>
    <!--Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/4da45c7bdd.js" crossorigin="anonymous"></script> <!-- Font Awesome itT -->
    <link id="pagestyle" href="/cms_ivss/public/assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" /> <!-- CSS Files -->
</head>

<body class="g-sidenav-show bg-gray-100">

    <?php include('includes/sidebar.php') ?> <!-- Sidebar -->

    <main class="main-content position-relative border-radius-lg ">

        <?php include('includes/navbar.php') ?> <!-- Navbar -->

        <!-- Profile -->
        <div class="container-fluid py-4">
            <form id="profileForm" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Form Edit -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h5 class="mb-0">Edit Profil</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-control-label">Nama</label>
                                        <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-control-label">Email</label>
                                        <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                    </div>
                                    <?php if ($user['role'] == 'dosen' || $user['role'] == 'kepala'): ?>
                                        <div class="col-md-6"><label class="form-control-label">NIP</label><input class="form-control" type="text" value="<?= htmlspecialchars($user['nip'] ?? '-') ?>" readonly></div>
                                        <div class="col-md-6"><label class="form-control-label">NIDN</label><input class="form-control" type="text" value="<?= htmlspecialchars($user['nidn'] ?? '-') ?>" readonly></div>
                                        <div class="col-md-6"><label class="form-control-label">Jabatan</label><input class="form-control" type="text" value="<?= htmlspecialchars($user['job_position'] ?? '-') ?>" readonly></div>
                                    <?php elseif ($user['role'] == 'mahasiswa'): ?>
                                        <div class="col-md-6"><label class="form-control-label">NIM</label><input class="form-control" type="text" value="<?= htmlspecialchars($user['nim'] ?? '-') ?>" readonly></div>
                                    <?php endif; ?>
                                    <div class="col-md-6">
                                        <label class="form-control-label">Program Studi</label>
                                        <input class="form-control" type="text" value="<?= htmlspecialchars($user['study_program_name'] ?? '-') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-control-label">Foto</label>
                                        <input class="form-control" type="file" name="photo">
                                    </div>
                                </div>

                                <hr class="horizontal dark">
                                <p class="text-uppercase text-sm">Informasi Kontak</p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-control-label">Alamat</label>
                                        <input class="form-control" type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                                    </div>

                                    <?php if (!empty($user['social_media'])): ?>
                                        <?php foreach ($user['social_media'] as $sm): ?>
                                            <div class="col-md-6 mt-3">
                                                <label class="form-control-label"><?= htmlspecialchars($sm['name'] ?? 'N/A') ?></label>
                                                <input class="form-control" type="text"
                                                    name="social_media[<?= htmlspecialchars($sm['link']) ?>]"
                                                    value="<?= htmlspecialchars($sm['link'] ?? '-') ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <div class="col-md-12 text-end mt-3">
                                        <button type="button" class="btn btn-sm py-1 px-3 btn-link text-info"
                                            data-bs-toggle="modal" data-bs-target="#modal-sosmed">
                                            <i class="fa-solid fa-plus-circle me-1"></i> Tambah Media Sosial
                                        </button>
                                    </div>
                                </div>

                                <hr class="horizontal dark">
                                <div class="col d-flex align-items-center justify-content-between px-0 mx-0">
                                    <p class="text-uppercase text-sm">Riwayat Pendidikan & Sertifikasi</p>
                                    <button type="button" class="btn btn-sm py-1 px-2 btn-link text-success"
                                        data-bs-toggle="modal" data-bs-target="#modal-list-edit"
                                        data-list-type="pendidikan_sertifikat">
                                        <i class="fa-solid fa-pencil me-1"></i> Edit Data
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-control-label">Pendidikan</label>
                                        <ul class="list-group">
                                            <?php if (!empty($user['educations'])): ?>
                                                <?php foreach ($user['educations'] as $p): ?>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong><?= htmlspecialchars($p['level'] ?? '-') ?></strong> -
                                                        <?= htmlspecialchars($p['major'] ?? '-') ?> (<?= htmlspecialchars($p['graduation_year'] ?? '-') ?>)
                                                        <br><?= htmlspecialchars($p['institution_name'] ?? '-') ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li class="list-group-item border-0 ps-0 text-sm">Belum ada data pendidikan</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-control-label">Sertifikasi</label>
                                        <ul class="list-group">
                                            <?php if (!empty($user['certificates'])): ?>
                                                <?php foreach ($user['certificates'] as $s): ?>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <strong><?= htmlspecialchars($s['name'] ?? '-') ?></strong>
                                                        (<?= htmlspecialchars($s['issue_year'] ?? '-') ?>)
                                                        <br><?= htmlspecialchars($s['issuer'] ?? '-') ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li class="list-group-item border-0 ps-0 text-sm">Belum ada sertifikat</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>

                                <hr class="horizontal dark">
                                <div class="col d-flex align-items-center justify-content-between px-0 mx-0">
                                    <p class="text-uppercase text-sm">Keahlian & Mata Kuliah</p>
                                    <button type="button" class="btn btn-sm py-1 px-2 btn-link text-success"
                                        data-bs-toggle="modal" data-bs-target="#modal-list-edit"
                                        data-list-type="keahlian_mk">
                                        <i class="fa-solid fa-pencil me-1"></i> Edit Data
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-control-label">Keahlian</label>
                                        <ul class="list-group">
                                            <?php if (!empty($user['skills'])): ?>
                                                <?php foreach ($user['skills'] as $k_item): ?>
                                                    <li class="list-group-item border-0 ps-0 text-sm">
                                                        <?= htmlspecialchars($k_item) ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li class="list-group-item border-0 ps-0 text-sm">Belum ada data keahlian</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <?php if ($user['role'] == 'kepala' || $user['role'] == 'dosen'): ?>
                                        <div class="col-md-6">
                                            <label class="form-control-label">Mata Kuliah</label>
                                            <ul class="list-group">
                                                <?php if (!empty($user['courses'])): ?>
                                                    <?php foreach ($user['courses'] as $mk_item): ?>
                                                        <li class="list-group-item border-0 ps-0 text-sm">
                                                            <?= htmlspecialchars($mk_item) ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li class="list-group-item border-0 ps-0 text-sm">Belum ada data mata kuliah</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan Data Dasar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Form Edit -->

                    <!-- Summary Profile -->
                    <div class="col-md-4 mb-4">
                        <div class="card card-profile">
                            <div class="row justify-content-center mt-3">
                                <div class="col-12 text-center">
                                    <img src="<?= BASE_URL . '/uploads/profile/' . ($user['photo'] ?? '') ?>" class="rounded-circle img-fluid border border-2 border-white shadow" style="width: 150px; height: 150px; object-fit: cover">
                                </div>
                            </div>

                            <div class="card-body pt-0 text-center">
                                <h5 class="mb-0 mt-3"><?= htmlspecialchars($user['name'] ?? 'N/A') ?></h5>
                                <div class="text-muted text-sm mb-3">
                                    <i class="fa fa-user-circle me-1"></i>
                                    <?= ucfirst(htmlspecialchars($user['role'] ?? 'N/A')) ?> |
                                    <i class="fa fa-graduation-cap me-1"></i>
                                    <?= htmlspecialchars($user['study_program_name'] ?? '-') ?>
                                </div>

                                <hr class="horizontal dark my-2">

                                <ul class="list-group list-group-flush text-start mb-3">
                                    <?php if ($user['role'] == 'dosen' || $user['role'] == 'kepala'): ?>
                                        <li class="list-group-item border-0 px-0 pt-0">
                                            <i class="fa fa-id-card me-2 text-primary"></i>
                                            <strong class="text-sm">NIP:</strong>
                                            <span class="float-end text-sm text-dark font-weight-bold"><?= htmlspecialchars($user['nip'] ?? '-') ?></span>
                                        </li>
                                        <li class="list-group-item border-0 px-0">
                                            <i class="fa fa-university me-2 text-primary"></i>
                                            <strong class="text-sm">Jabatan:</strong>
                                            <span class="float-end text-sm text-dark font-weight-bold"><?= htmlspecialchars($user['job_position'] ?? '-') ?></span>
                                        </li>
                                    <?php elseif ($user['role'] == 'mahasiswa'): ?>
                                        <li class="list-group-item border-0 px-0 pt-0">
                                            <i class="fa fa-id-badge me-2 text-primary"></i>
                                            <strong class="text-sm">NIM:</strong>
                                            <span class="float-end text-sm text-dark font-weight-bold"><?= htmlspecialchars($user['nim'] ?? '-') ?></span>
                                        </li>
                                    <?php endif; ?>
                                    <li class="list-group-item border-0 px-0 pt-0">
                                        <i class="fa fa-envelope me-2 text-info"></i>
                                        <strong class="text-sm">Email:</strong>
                                        <span class="float-end text-sm"><?= htmlspecialchars($user['email'] ?? 'N/A') ?></span>
                                    </li>
                                    <li class="list-group-item border-0 px-0 pb-0">
                                        <i class="fa fa-map-marker me-2 text-warning"></i>
                                        <strong class="text-sm">Alamat:</strong>
                                        <span class="float-end text-sm text-truncate" style="max-width: 60%;"><?= htmlspecialchars($user['address'] ?? 'Belum diisi') ?></span>
                                    </li>
                                </ul>

                                <hr class="horizontal dark my-3">
                                <p class="text-uppercase text-xs text-secondary mb-2">Statistik Kontribusi</p>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h6 class="font-weight-bolder text-primary">5</h6>
                                        <p class="text-sm mb-0 text-secondary">Berita</p>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="font-weight-bolder text-info">12</h6>
                                        <p class="text-sm mb-0 text-secondary">Publikasi</p>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="font-weight-bolder text-success">7</h6>
                                        <p class="text-sm mb-0 text-secondary">Mhs Bim.</p>
                                    </div>
                                </div>

                                <hr class="horizontal dark my-3">

                                <p class="text-uppercase text-xs text-secondary mb-2">Social Media</p>
                                <div class="d-flex justify-content-center">
                                    <?php if (!empty($user['social_media'])): ?>
                                        <?php foreach ($user['social_media'] as $sm):
                                            $name = strtolower(htmlspecialchars($sm['name'] ?? ''));
                                            $icon_class =
                                                (strpos($name, 'linkedin') !== false) ? 'fa-brands fa-linkedin' : ((strpos($name, 'instagram') !== false) ? 'fa-brands fa-instagram' : ((strpos($name, 'scholar') !== false) ? 'fa-brands fa-google-scholar' : ((strpos($name, 'sinta') !== false) ? 'fa-brands fa-readme' : 'fa-solid fa-globe')));

                                        ?>
                                            <a href="<?= htmlspecialchars($sm['link'] ?? '#') ?>" target="_blank" class="text-secondary mx-2" title="<?= htmlspecialchars($sm['nama_sosmed'] ?? 'Media Sosial') ?>">
                                                <i class="fa <?= $icon_class ?> fa-lg text-secondary"></i>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-xs text-muted">Belum ada media sosial</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Summary Profile -->
                </div>
            </form>
        </div>
        <!-- End Profile -->

        <!-- Modal Sosmed -->
        <div class="modal fade" id="modal-sosmed" tabindex="-1" role="dialog" aria-labelledby="modal-sosmed-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-sosmed-label">Kelola Media Sosial</h5>
                    </div>
                    <form id="sosmed-add-form" action="<?= BASE_URL ?>/profile/add-sosmed" method="POST">
                        <div class="modal-body">
                            <p class="text-uppercase text-sm mb-2">Media Sosial Tersimpan</p>
                            <div id="sosmed-list-container">
                                <p class="text-center text-muted">Memuat data...</p>
                            </div>

                            <hr class="horizontal dark mt-4 mb-3">
                            <p class="text-uppercase text-sm mb-2">Tambah Item Baru</p>
                            <div class="row" id="new-sosmed-template">
                                <div class="col-md-5 mb-3">
                                    <input class="form-control" type="text" id="new_sosmed_name" placeholder="Nama Platform (Contoh: LinkedIn)">
                                </div>
                                <div class="col-md-7 mb-3">
                                    <div class="input-group">
                                        <input class="form-control" type="url" id="new_sosmed_link" placeholder="https://...">
                                        <!-- <input type="url" class="form-control" placeholder="Link URL..." required> -->
                                        <button type="button" class="btn btn-outline-success m-0 px-3 py-2" id="add-sosmed-btn">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="new-sosmed-data-container"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn bg-gradient-primary">Simpan Semua Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal Sosmed -->

        <!-- Modal List Dinamic Item -->
        <div class="modal fade" id="modal-list-edit" tabindex="-1" role="dialog" aria-labelledby="modal-list-edit-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-list-edit-label">Edit Data <span id="modal-title-type"></span></h5>
                    </div>
                    <form id="list-edit-form" action="<?= BASE_URL ?>/profile/update-list" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="list_type" id="list-type-input">

                            <div id="modal-content-area" class="pb-3">
                                <p class="text-center text-muted">Memuat data...</p>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn bg-gradient-primary">Simpan Semua Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal List Dinamic Item -->

        <!-- Modal Alert -->
        <div class="modal fade" id="modal-alert" tabindex="-1" aria-labelledby="alertLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold" id="alertLabel">Pesan</h6>
                    </div>
                    <div class="modal-body text-center py-3">
                        <i id="alert-icon" class="fa fa-info-circle text-primary mb-3" style="font-size: 2rem;"></i>
                        <p id="alert-message" class="mb-0 text-sm"></p>
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-center">
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Alert -->

        <!-- Modal Konfirmasi -->
        <div class="modal fade" id="modal-confirm" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold" id="confirmLabel">Konfirmasi</h6>
                    </div>
                    <div class="modal-body text-center py-3">
                        <i id="confirm-icon" class="fa fa-question-circle text-warning mb-3" style="font-size: 2rem;"></i>
                        <p id="confirm-message" class="mb-0 text-sm"></p>
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-center">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary" id="confirm-yes">Ya</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Konfirmasi -->

        <?php include('includes/footer.php') ?> <!-- Footer -->

        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> <!-- Link Jquery -->
        <script>
            // Data user global
            const USER_DATA = <?= json_encode($user) ?>;
            const BASE_URL = '<?= BASE_URL ?>';
            let nextNewSosmedIndex = 0; // Index untuk Sosmed Baru

            // Javascript Function Helpers
            // Show Edit Form Pendidikan & Sertifikasi
            function generateListItemForm(type, data, index) {
                let html = '';
                const title = type === 'pendidikan' ? 'Pendidikan' : 'Sertifikasi';
                const color = type === 'pendidikan' ? 'text-primary' : 'text-info';

                // Define Delete Button
                let deleteButton = `<button class="btn btn-sm btn-danger px-3 py-1 mb-0 delete-item-btn" type="button"><i class="fa fa-times"></i> Hapus</button>`;

                if (type === 'pendidikan') { // Form Pendidikan
                    html = `
                    <div class="card card-body border p-3 mb-3 item-list" data-index="${index}" data-id="${data.id || ''}">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-sm ${color} mb-0">${title}</h6>
                            ${deleteButton}
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-control-label">Jenjang</label>
                                <input class="form-control" type="text" name="pendidikan[${index}][level]" value="${data.level || ''}" placeholder="S1/S2/S3" required></div>
                            <div class="col-md-6 mb-3"><label class="form-control-label">Jurusan</label>
                                <input class="form-control" type="text" name="pendidikan[${index}][major]" value="${data.major || ''}" placeholder="Teknik Informatika" required></div>
                            <div class="col-md-9 mb-3"><label class="form-control-label">Institusi</label>
                                <input class="form-control" type="text" name="pendidikan[${index}][institution_name]" value="${data.institution_name || ''}" placeholder="Universitas X" required></div>
                            <div class="col-md-3 mb-3"><label class="form-control-label">Tahun Lulus</label>
                                <input class="form-control" type="number" name="pendidikan[${index}][graduation_year]" value="${data.graduation_year || ''}" placeholder="2020" required></div>
                        </div>
                    </div>`;
                } else if (type === 'sertifikat') { // Form Sertifikasi
                    html = `
                    <div class="card card-body border p-3 mb-3 item-list" data-index="${index}" data-id="${data.id || ''}">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-sm ${color} mb-0">${title}</h6>
                            ${deleteButton}
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3"><label class="form-control-label">Nama Sertifikat</label>
                                <input class="form-control" type="text" name="sertifikat[${index}][name]" value="${data.name || ''}" required></div>
                            <div class="col-md-8 mb-3"><label class="form-control-label">Penerbit</label>
                                <input class="form-control" type="text" name="sertifikat[${index}][issuer]" value="${data.issuer || ''}" required></div>
                            <div class="col-md-4 mb-3"><label class="form-control-label">Tahun Terbit</label>
                                <input class="form-control" type="number" name="sertifikat[${index}][issue_year]" value="${data.issue_year || ''}" required></div>
                        </div>
                    </div>`;
                }
                return html;
            }

            // Show Edit Form Keahlian & MK
            function generateKeahlianMKForm(type, item, index) {
                const title = type === 'keahlian' ? 'Keahlian' : 'Mata Kuliah';
                const placeholder = type === 'keahlian' ? 'Contoh: Data Mining' : 'Contoh: Algoritma dan Struktur Data';
                return `
                    <div class="input-group mb-2 item-list-simple" data-index="${index}">
                    <span class="input-group-text text-sm" style="width: 100px;">${title}</span>
                    <input type="text" class="form-control" name="${type}[]" value="${item || ''}" placeholder="${placeholder}" required>
                    <button class="btn btn-outline-danger mb-0 delete-simple-item-btn" type="button"><i class="fa fa-times"></i></button>
                    </div>`;
            }

            // Show Edit Form Sosmed
            function generateSosmedEditForm(item, index) {
                return `
                    <div class="input-group mb-2 sosmed-item-edit" data-index="${index}">
                        <span class="input-group-text text-sm" style="width: 120px;">${item.name}</span>
                        <input type="hidden" name="social_media[${index}][name]" value="${item.name}">
                        <input type="url" class="form-control" name="social_media[${index}][link]" value="${item.link || ''}" placeholder="Link URL" required>
                        <button class="btn btn-outline-danger delete-sosmed-btn mb-0" type="button">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>`;
            }

            // Show New Form Sosmed
            function generateNewSosmedForm(index, name, link) {
                return `
                    <div class="input-group mb-2 new-sosmed-item" data-index="${index}">
                        <span class="input-group-text text-sm" style="width: 120px;">${name}</span>
                        <input type="hidden" name="social_media_new[${index}][name]" value="${name}">
                        <input type="url" class="form-control" name="social_media_new[${index}][link]" value="${link}" required>
                        <button class="btn btn-outline-danger delete-new-sosmed-btn mb-0" type="button">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>`;
            }

            // Show Alert
            function showAlert(message, type) {
                const icon = {
                    success: 'fa-check-circle text-success',
                    error: 'fa-circle-xmark text-danger',
                    warning: 'fa-exclamation-triangle text-warning',
                    info: 'fa-info-circle text-primary'
                };

                $('#alert-icon').attr('class', `fa ${icon[type]} mb-3`).css('font-size', '2rem');
                $('#alert-message').html(message);

                const modal = new bootstrap.Modal(document.getElementById('modal-alert'));
                modal.show();
            }

            // Show Confirm
            function showConfirm(message, callback) {
                $('#confirm-message').html(message);

                const modal = new bootstrap.Modal(document.getElementById('modal-confirm'));
                modal.show();

                $('#confirm-yes').off('click').on('click', function() {
                    modal.hide();
                    if (typeof callback === 'function') callback();
                });
            }
            // End of Helper Functions

            // Javascript Event Handlers
            // Handler Modal Media Sosial 
            $('#modal-sosmed').on('show.bs.modal', function() {
                const sosmedContainer = $('#sosmed-list-container');
                sosmedContainer.empty();
                $('#new-sosmed-data-container').empty();
                nextNewSosmedIndex = 0;

                let sosmedList = (USER_DATA.social_media || []).map((item, i) =>
                    generateSosmedEditForm(item, i)
                ).join('');

                sosmedContainer.html(
                    sosmedList || `<p class="text-center text-muted">Belum ada media sosial tersimpan.</p>`
                );

                // reset template
                $('#new_sosmed_name').val('');
                $('#new_sosmed_link').val('');
            });

            // Handler untuk menambahkan item sosmed baru ke container
            $(document).on('click', '#add-sosmed-btn', function() {
                const name = $('#new_sosmed_name').val().trim();
                const link = $('#new_sosmed_link').val().trim();

                if (!name || !link) {
                    showAlert('Nama Platform dan Link wajib diisi');
                    return;
                }

                const newIndex = nextNewSosmedIndex++;

                $('#new-sosmed-data-container').append(
                    generateNewSosmedForm(newIndex, name, link)
                );

                $('#new_sosmed_name').val('');
                $('#new_sosmed_link').val('');
            });


            // Handler Hapus item Sosmed yang sudah ada
            $(document).on('click', '.delete-sosmed-btn', function() {
                const row = $(this).closest('.sosmed-item-edit');
                showConfirm('Hapus item media sosial ini?', () => row.remove());
            });


            // Handler Hapus item Sosmed BARU
            $(document).on('click', '.delete-new-sosmed-btn', function() {
                const row = $(this).closest('.new-sosmed-item');
                showConfirm('Hapus item media sosial baru ini?', () => row.remove());
            });


            // Handler untuk Modal List Dinamis (Pendidikan/Sertifikasi/Keahlian)
            $('#modal-list-edit').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const listType = button.data('list-type');
                const modal = $(this);
                const contentArea = $('#modal-content-area');
                contentArea.empty();

                let title = '';
                modal.find('#list-type-input').val(listType);

                // Handler untuk Pendidikan & Sertifikasi (Accordion + Hapus)
                if (listType === 'pendidikan_sertifikat') {
                    title = 'Pendidikan & Sertifikasi';

                    // Pendidikan
                    let educationForms = (USER_DATA.educations || []).map((item, i) =>
                        generateListItemForm('pendidikan', item, `pend_${item.id_pendidikan || i}`)
                    ).join('');

                    // Sertifikasi
                    let certificationForms = (USER_DATA.certificates || []).map((item, i) =>
                        generateListItemForm('sertifikat', item, `cert_${item.id_sertifikat || i}`)
                    ).join('');

                    modal.find('#modal-title-type').text(title);
                    contentArea.html(`
                        <div class="accordion" id="dataAccordion">
                            <div class="accordion-item"> 
                                <h2 class="accordion-header" id="headingPendidikan">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePendidikan" aria-expanded="false" aria-controls="collapsePendidikan">
                                        Riwayat Pendidikan (${(USER_DATA.educations || []).length} Data)
                                    </button>
                                </h2>
                                <div id="collapsePendidikan" class="accordion-collapse collapse" aria-labelledby="headingPendidikan" data-bs-parent="#dataAccordion">
                                    <div class="accordion-body">
                                        <div id="pendidikan-container">${educationForms || '<p class="text-muted">Belum ada data. Tekan "Tambah Pendidikan" di bawah.</p>'}</div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-3 add-item-btn" data-type="pendidikan"><i class="fa fa-plus me-1"></i> Tambah Pendidikan</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingSertifikat">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSertifikat" aria-expanded="false" aria-controls="collapseSertifikat">
                                        Daftar Sertifikasi (${(USER_DATA.certificates || []).length} Data)
                                    </button>
                                </h2>
                                <div id="collapseSertifikat" class="accordion-collapse collapse" aria-labelledby="headingSertifikat" data-bs-parent="#dataAccordion">
                                    <div class="accordion-body">
                                        <div id="sertifikat-container">${certificationForms || '<p class="text-muted">Belum ada data. Tekan "Tambah Sertifikasi" di bawah.</p>'}</div>
                                        <button type="button" class="btn btn-sm btn-outline-info mt-3 add-item-btn" data-type="sertifikat"><i class="fa fa-plus me-1"></i> Tambah Sertifikasi</button>
                                    </div>
                                </div>
                            </div>
                        </div>`);
                } else if (listType === 'keahlian_mk') {
                    title = 'Keahlian & Mata Kuliah';
                    let keahlianHtml = (USER_DATA.skills || []).map((item, i) => generateKeahlianMKForm('keahlian', item, `keahlian_${i}`)).join('');
                    let mkHtml = (USER_DATA.courses || []).map((item, i) => generateKeahlianMKForm('mata_kuliah', item, `mk_${i}`)).join('');
                    modal.find('#modal-title-type').text(title);

                    contentArea.html(`
                        <div class="accordion" id="keahlianMkAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingKeahlian">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKeahlian" aria-expanded="true" aria-controls="collapseKeahlian">
                                        Keahlian (${(USER_DATA.skills || []).length} Data)
                                    </button>
                                </h2>
                                <div id="collapseKeahlian" class="accordion-collapse collapse show" aria-labelledby="headingKeahlian" data-bs-parent="#keahlianMkAccordion">
                                    <div class="accordion-body">
                                        <div id="keahlian-list-container">${keahlianHtml}</div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-3 add-item-btn" data-type="keahlian"><i class="fa fa-plus me-1"></i> Tambah Keahlian</button>
                                    </div>
                                </div>
                            </div>
                            <?php if ($user['role'] == 'kepala' || $user['role'] == 'dosen'): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingMK">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMK" aria-expanded="false" aria-controls="collapseMK">
                                        Mata Kuliah Diampu (${(USER_DATA.courses || []).length} Data)
                                    </button>
                                </h2>
                                <div id="collapseMK" class="accordion-collapse collapse" aria-labelledby="headingMK" data-bs-parent="#keahlianMkAccordion">
                                    <div class="accordion-body">
                                        <div id="mk-list-container">${mkHtml}</div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-3 add-item-btn" data-type="mata_kuliah"><i class="fa fa-plus me-1"></i> Tambah Mata Kuliah</button>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div> `);
                }
            });

            // Handler Tambah Item Baru (Pendidikan/Sertifikasi/Keahlian/MK)
            $(document).on('click', '.add-item-btn', function() {
                const type = $(this).data('type');
                const newIndex = 'NEW_' + Date.now();
                let newFormHtml = '';

                if (type === 'pendidikan' || type === 'sertifikat') {
                    newFormHtml = generateListItemForm(type, {}, newIndex);
                    $(`#${type}-container`).append(newFormHtml);
                } else if (type === 'keahlian') {
                    newFormHtml = generateKeahlianMKForm(type, '', newIndex);
                    $('#keahlian-list-container').append(newFormHtml);
                } else if (type === 'mata_kuliah') {
                    newFormHtml = generateKeahlianMKForm(type, '', newIndex);
                    $('#mk-list-container').append(newFormHtml);
                }
            });

            // Handler Hapus Item (untuk Pendidikan/Sertifikat)
            $(document).on('click', '.delete-item-btn', function() {
                const btn = $(this);
                showConfirm('Yakin ingin menghapus item ini?', function() {
                    btn.closest('.item-list').remove();
                });
            });

            // Handler Hapus Item (untuk Keahlian/MK)
            $(document).on('click', '.delete-simple-item-btn', function() {
                const btn = $(this);
                showConfirm('Yakin ingin menghapus item ini?', function() {
                    btn.closest('.input-group').remove();
                });
            });

            // Handler AJAX: UPDATE SOSIAL MEDIA
            $('#sosmed-add-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: BASE_URL + '/profile/add-sosmed',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',

                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            $('#modal-sosmed').modal('hide');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            showAlert('Gagal: ' + response.message, 'error');
                        }
                    },

                    error: function(xhr) {
                        showAlert('Kesalahan server: ' + xhr.responseText, 'warning');
                    }
                });
            });


            // Handler AJAX: UPDATE LIST DINAMIS
            $('#list-edit-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = form.serialize();

                $.ajax({
                    url: BASE_URL + '/profile/update-list',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            $('#modal-list-edit').modal('hide');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('Terjadi kesalahan saat menyimpan data list: ' + xhr.responseText, 'warning');
                    }
                });
            });

            // Handler AJAX: UPDATE FORM BASIC
            $("#profileForm").on("submit", function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: BASE_URL + '/profile/update',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('Terjadi kesalahan saat menyimpan data: ' + xhr.responseText, 'warning');
                    }
                });
            });
            // End of Handler Function

            // Sidenav Scrollbar
            var win = navigator.platform.indexOf('Win') > -1;
            if (win && document.querySelector('#sidenav-scrollbar')) {
                var options = {
                    damping: '0.5'
                }
                Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
            }
        </script>
        <script src="/cms_ivss/public/assets/js/core/popper.min.js"></script>
        <script script src="/cms_ivss/public/assets/js/core/bootstrap.min.js"></script>
        <script src="/cms_ivss/public/assets/js/plugins/perfect-scrollbar.min.js"></script>
        <script src="/cms_ivss/public/assets/js/plugins/smooth-scrollbar.min.js"></script>
        <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
        <script src="/cms_ivss/public/assets/js/argon-dashboard.min.js?v=2.1.0"></script>
        <!-- Github buttons -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>