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
  <script src="https://kit.fontawesome.com/4da45c7bdd.js" crossorigin="anonymous"></script>
  <link id="pagestyle" href="/cms_ivss/public/assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .badge-category {
      font-size: 0.65rem;
    }
    .bootcamp-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: 1px solid #e9ecef;
    }
    .bootcamp-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .bootcamp-image {
      height: 180px;
      object-fit: cover;
      width: 100%;
    }
    .description-truncate {
      display: -webkit-box;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .author-badge {
      background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%);
      color: white;
      font-size: 0.7rem;
      padding: 2px 8px;
    }
    .btn-action-group {
      display: flex;
      gap: 4px;
    }
    .btn-action-group .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
      line-height: 1.2;
      min-width: 30px;
    }
    .btn-action-group .btn i {
      font-size: 0.75rem;
    }
    .btn-visit {
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
    }
  </style>
</head>

<body class="g-sidenav-show  bg-gray-100">

  <?php include('includes/sidebar.php') ?>

  <main class="main-content position-relative border-radius-lg ">

    <?php include('includes/navbar.php') ?>

    <!-- Bootcamp -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="m-0">Bootcamp</h5>
              </div>
              <div>
                <span class="badge bg-gradient-primary" id="total-count">0 bootcamp</span>
              </div>
            </div>
          </div>

          <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-4">
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                  <input type="text" class="form-control" placeholder="Cari bootcamp berdasarkan nama atau deskripsi..." id="searchBootcamp">
                </div>
              </div>
              <div class="col-md-3">
                <select id="categoryFilter" class="form-control">
                  <option value="">Semua Kategori</option>
                  <!-- Options akan diisi oleh JavaScript -->
                </select>
              </div>
              <div class="col-md-1">
                <button class="btn btn-success w-100" id="btn-add-bootcamp" data-bs-toggle="modal" data-bs-target="#modal-add-bootcamp" title="Tambah Bootcamp">
                  <i class="fa fa-plus"></i>
                </button>
              </div>
            </div>

            <!-- Cards Container -->
            <div class="row g-4" id="bootcamp-container">
              <!-- Cards will be loaded here -->
              <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Memuat data bootcamp...</p>
              </div>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="d-flex justify-content-center mt-4"></div>

            <!-- No Data Message (hidden by default) -->
            <div id="no-data-message" class="text-center py-5 d-none">
              <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">Tidak ada data bootcamp</h5>
              <p class="text-muted">Mulai dengan menambahkan bootcamp pertama Anda</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Bootcamp -->
  </main>

  <!-- Modal: Tambah Bootcamp -->
  <div class="modal fade" id="modal-add-bootcamp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form id="form-add-bootcamp" class="modal-content" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Bootcamp</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="add-bootcamp-alert"></div>
          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label class="form-label">Nama Bootcamp <span class="text-danger">*</span></label>
                <input name="name" type="text" class="form-control" placeholder="Contoh: Bootcamp Web Development" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Link Bootcamp <span class="text-danger">*</span></label>
                <input name="link" type="url" class="form-control" placeholder="https://example.com" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" cols="30" rows="4" placeholder="Deskripsikan bootcamp secara detail..." required></textarea>
          </div>
          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="categories[]" class="form-control categories-select" multiple style="width: 100%;">
                  <!-- Data diisi JavaScript -->
                </select>
                <small class="text-muted">Pilih satu atau lebih kategori (opsional)</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-control-label">Foto <span class="text-danger">*</span></label>
                <input class="form-control" type="file" name="photo" accept="image/*" required>
                <small class="text-muted">Format: JPG, PNG, GIF, WebP</small>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal: Edit Bootcamp -->
  <div class="modal fade" id="modal-edit-bootcamp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form id="form-edit-bootcamp" class="modal-content" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="old_photo" id="edit-old-photo">
        <div class="modal-header">
          <h5 class="modal-title">Edit Bootcamp</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="edit-bootcamp-alert"></div>
          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label class="form-label">Nama Bootcamp <span class="text-danger">*</span></label>
                <input name="name" id="edit-name" type="text" class="form-control" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Link Bootcamp <span class="text-danger">*</span></label>
                <input name="link" id="edit-link" type="url" class="form-control" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
            <textarea name="description" id="edit-description" class="form-control" cols="30" rows="4" required></textarea>
          </div>
          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="categories[]" id="edit-categories" class="form-control" multiple style="width: 100%;">
                  <!-- Options akan diisi oleh JavaScript -->
                </select>
                <small class="text-muted">Pilih satu atau lebih kategori (opsional)</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="text-center">
                <img id="edit-preview" class="img-fluid rounded mb-2" style="max-height: 120px;">
                <p class="text-sm text-muted mb-0">Foto saat ini</p>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Foto Baru (opsional)</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Detail Bootcamp -->
  <div class="modal fade" id="modal-detail-bootcamp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Bootcamp</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-4">
            <img id="detail-preview" class="img-fluid rounded" style="max-height: 300px; width: 100%; object-fit: cover;">
          </div>
          <h4 id="detail-name" class="mb-3"></h4>
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="mb-2">
                <strong><i class="fas fa-user me-2"></i>Dibuat oleh:</strong>
                <span id="detail-author" class="ms-2"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-2">
                <strong><i class="fas fa-calendar me-2"></i>Tanggal:</strong>
                <span id="detail-date" class="ms-2"></span>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <strong><i class="fas fa-tags me-2"></i>Kategori:</strong>
            <span id="detail-categories" class="ms-2"></span>
          </div>
          <div class="mb-4">
            <strong><i class="fas fa-link me-2"></i>Link Bootcamp:</strong>
            <a id="detail-link" href="#" target="_blank" class="ms-2 d-inline-block text-truncate" style="max-width: 300px;"></a>
          </div>
          <div class="card">
            <div class="card-header">
              <strong><i class="fas fa-align-left me-2"></i>Deskripsi</strong>
            </div>
            <div class="card-body">
              <p id="detail-description" class="mb-0"></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Alert & Konfirmasi -->
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

  <?php require_once 'includes/footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";
    const DEFAULT_LIMIT = 9;
    let currentPage = 1;
    let currentSearch = '';
    let currentCategory = '';
    let globalCategories = [];
    let currentUserIsAdmin = false;

    // Helper functions
    function showAlert(message, type = 'info') {
      const icons = {
        success: 'fa-check-circle text-success',
        error: 'fa-circle-xmark text-danger',
        warning: 'fa-exclamation-triangle text-warning',
        info: 'fa-info-circle text-primary'
      };
      $('#alert-icon').attr('class', `fa ${icons[type] || icons.info} mb-3`).css('font-size', '1.6rem');
      $('#alert-message').html(message);
      const modal = new bootstrap.Modal($('#modal-alert')[0]);
      modal.show();
      setTimeout(() => {
        modal.hide();
      }, 3000);
    }

    function showConfirm(message, callback) {
      $('#confirm-message').html(message);
      const modalEl = $('#modal-confirm')[0];
      const modal = new bootstrap.Modal(modalEl);
      $('#confirm-yes').off('click').on('click', function() {
        modal.hide();
        if (typeof callback === 'function') callback(true);
      });
      modal.show();
    }

    // Format date
    function formatDate(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }

    // Load category list
    function loadCategories() {
      $.ajax({
        url: BASE_URL + '/bootcamp/categories',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            globalCategories = res.data;

            // Initialize Select2 untuk form tambah
            $('.categories-select').select2({
              placeholder: 'Pilih kategori...',
              allowClear: true,
              data: globalCategories.map(c => ({
                id: c.id,
                text: c.name
              })),
              tags: false,
              dropdownParent: $('#modal-add-bootcamp')
            });

            // Isi dropdown filter kategori
            const $categoryFilter = $('#categoryFilter');
            $categoryFilter.empty().append('<option value="">Semua Kategori</option>');
            globalCategories.forEach(cat => {
              $categoryFilter.append(`<option value="${cat.id}">${cat.name}</option>`);
            });
          }
        },
        error: function(xhr) {
          console.error('Error loading categories:', xhr);
          showAlert('Gagal memuat daftar kategori', 'error');
        }
      });
    }

    // Function untuk menampilkan modal edit
    function openEditModal(bootcampId) {
      // Fetch data dari API
      $.ajax({
        url: BASE_URL + "/bootcamp/getDetail",
        method: "GET",
        data: { id: bootcampId },
        dataType: "json",
        success: function(res) {
          if (res.success && res.data) {
            const bootcamp = res.data;
            
            // Isi data ke form
            $('#edit-id').val(bootcamp.id);
            $('#edit-name').val(bootcamp.name);
            $('#edit-description').val(bootcamp.description);
            $('#edit-link').val(bootcamp.link);
            $('#edit-old-photo').val(bootcamp.photo || '');
            $('#edit-preview').attr('src', BASE_URL + '/uploads/bootcamp/' + (bootcamp.photo || 'default.jpg'));
            
            // Setup categories
            const $select = $('#edit-categories');
            $select.empty();
            
            // Add options from globalCategories
            globalCategories.forEach(cat => {
              $select.append(new Option(cat.name, cat.id, false, false));
            });
            
            // Select current categories
            let selectedCategoryIds = [];
            if (bootcamp.category_ids) {
              if (Array.isArray(bootcamp.category_ids)) {
                selectedCategoryIds = bootcamp.category_ids;
              } else if (typeof bootcamp.category_ids === 'string') {
                selectedCategoryIds = bootcamp.category_ids.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
              }
            }
            
            // Initialize Select2
            if ($select.hasClass('select2-hidden-accessible')) {
              $select.select2('destroy');
            }
            
            $select.select2({
              dropdownParent: $('#modal-edit-bootcamp'),
              placeholder: "Pilih kategori...",
              allowClear: true,
              tags: false
            });
            
            // Set values after Select2 is initialized
            setTimeout(() => {
              $select.val(selectedCategoryIds).trigger('change');
            }, 100);
            
            // Tampilkan modal
            const modal = new bootstrap.Modal(document.getElementById('modal-edit-bootcamp'));
            modal.show();
            
          } else {
            showAlert(res.message || "Gagal memuat data bootcamp", "error");
          }
        },
        error: function(xhr, status, error) {
          console.error('Error loading bootcamp detail:', error);
          showAlert('Terjadi kesalahan saat memuat data bootcamp', 'error');
        }
      });
    }

    // Function untuk menampilkan modal detail
    function openDetailModal(bootcampId) {
      // Tampilkan modal dengan loading state
      $('#detail-preview').attr('src', BASE_URL + '/uploads/bootcamp/default.jpg');
      $('#detail-name').text('Memuat...');
      $('#detail-description').text('Memuat...');
      $('#detail-author').text('Memuat...');
      $('#detail-date').text('Memuat...');
      $('#detail-link').attr('href', '#').text('Memuat...');
      $('#detail-categories').html('<span class="text-muted">Memuat...</span>');
      
      const modal = new bootstrap.Modal(document.getElementById('modal-detail-bootcamp'));
      modal.show();
      
      // Fetch data dari API
      $.ajax({
        url: BASE_URL + "/bootcamp/getDetail",
        method: "GET",
        data: { id: bootcampId },
        dataType: "json",
        success: function(res) {
          if (res.success && res.data) {
            const bootcamp = res.data;
            
            // Update modal content
            $('#detail-name').text(bootcamp.name);
            $('#detail-description').text(bootcamp.description);
            $('#detail-author').text(bootcamp.author_name || 'Admin');
            $('#detail-date').text(formatDate(bootcamp.created_at));
            $('#detail-link').attr('href', bootcamp.link).text(bootcamp.link);
            $('#detail-preview').attr('src', BASE_URL + '/uploads/bootcamp/' + (bootcamp.photo || 'default.jpg'));
            
            // Format categories
            if (bootcamp.categories && bootcamp.categories.trim() !== '') {
              const categoriesHtml = bootcamp.categories.split(', ').map(cat =>
                `<span class="badge bg-gradient-info text-white me-1 mb-1 px-2 py-1">${cat}</span>`
              ).join('');
              $('#detail-categories').html(categoriesHtml);
            } else {
              $('#detail-categories').html('<span class="badge bg-gradient-secondary">Tidak ada kategori</span>');
            }
            
          } else {
            showAlert(res.message || "Gagal memuat detail bootcamp", "error");
            modal.hide();
          }
        },
        error: function(xhr, status, error) {
          console.error('Error loading bootcamp detail:', error);
          showAlert('Terjadi kesalahan saat memuat detail bootcamp', 'error');
          modal.hide();
        }
      });
    }

    // Function untuk menghapus bootcamp
    function deleteBootcampItem(bootcampId) {
      showConfirm("Yakin ingin menghapus bootcamp ini?", function(ok) {
        if (!ok) return;

        $.ajax({
          url: BASE_URL + "/bootcamp/delete",
          method: "POST",
          data: {
            id: bootcampId
          },
          dataType: "json",
          success: function(res) {
            showAlert(res.message, res.success ? "success" : "error");
            if (res.success) loadBootcamps(currentPage, currentSearch, currentCategory);
          },
          error: function(xhr, status, error) {
            console.error('Error deleting bootcamp:', error);
            showAlert('Terjadi kesalahan saat menghapus bootcamp', 'error');
          }
        });
      });
    }

    // Render bootcamp card dengan event delegation
    function renderBootcampCard(bc, isAdmin) {
      const categoriesHtml = bc.categories && bc.categories !== '' ?
        bc.categories.split(', ').map(cat =>
          `<span class="badge bg-gradient-info text-white me-1 mb-1 px-2 py-1 badge-category">${cat}</span>`
        ).join('') :
        '<span class="text-muted text-xs">Tidak ada kategori</span>';

      const createdDate = formatDate(bc.created_at);
      const imageUrl = bc.photo ? 
        BASE_URL + '/uploads/bootcamp/' + bc.photo : 
        BASE_URL + '/uploads/bootcamp/default.jpg';

      return `
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
          <div class="card bootcamp-card h-100" data-bootcamp-id="${bc.id}">
            <div class="position-relative">
              <img src="${imageUrl}" class="bootcamp-image" alt="${bc.name}" onerror="this.src='${BASE_URL}/uploads/bootcamp/default.jpg'">
              <div class="position-absolute top-0 end-0 m-2">
                <span class="badge author-badge">
                  <i class="fas fa-user me-1"></i>${bc.author_name || 'Admin'}
                </span>
              </div>
            </div>
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0 text-dark fw-bold text-truncate" title="${bc.name}">${bc.name}</h6>
                <small class="text-muted">
                  <i class="fas fa-calendar-alt me-1"></i>${createdDate}
                </small>
              </div>
              
              <div class="mb-3">
                <div class="d-flex flex-wrap">
                  ${categoriesHtml}
                </div>
              </div>
              
              <p class="text-muted text-xs description-truncate mb-3" title="${bc.description || 'Tidak ada deskripsi'}">
                ${bc.description || 'Tidak ada deskripsi'}
              </p>
              
              <div class="mt-auto">
                <div class="d-flex justify-content-between align-items-center">
                  <a href="${bc.link}" target="_blank" class="btn btn-sm bg-gradient-warning text-white btn-visit">
                    <i class="fas fa-external-link-alt me-1"></i> Link Pendaftaran
                  </a>
                  <div class="btn-action-group">
                    <button type="button" class="btn btn-sm btn-outline-info btn-detail" data-id="${bc.id}" title="Lihat Detail">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning btn-edit" data-id="${bc.id}" title="Edit Bootcamp">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="${bc.id}" title="Hapus Bootcamp">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // Event Delegation untuk semua tombol
    $(document).on('click', '.btn-detail', function() {
      const bootcampId = $(this).data('id');
      if (bootcampId) {
        openDetailModal(bootcampId);
      }
    });

    $(document).on('click', '.btn-edit', function() {
      const bootcampId = $(this).data('id');
      if (bootcampId) {
        openEditModal(bootcampId);
      }
    });

    $(document).on('click', '.btn-delete', function() {
      const bootcampId = $(this).data('id');
      if (bootcampId) {
        deleteBootcampItem(bootcampId);
      }
    });

    // Load bootcamps
    function loadBootcamps(page = 1, search = '', category = '', limit = DEFAULT_LIMIT) {
      const container = $('#bootcamp-container');
      const pagination = $('#pagination');
      const noDataMessage = $('#no-data-message');
      
      // Show loading
      container.html(`
        <div class="col-12 text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="text-muted mt-2">Memuat data bootcamp...</p>
        </div>
      `);
      pagination.empty();
      noDataMessage.addClass('d-none');

      const requestData = {
        page: page,
        limit: limit,
        search: search
      };

      if (category) {
        requestData.category_id = category;
      }

      $.ajax({
        url: BASE_URL + '/bootcamp/list',
        method: 'GET',
        dataType: 'json',
        data: requestData,
        success: function(res) {
          container.empty();
          
          // Cek apakah user adalah admin
          currentUserIsAdmin = res.is_admin || false;
          
          if (!res || !res.success || !res.data || res.data.length === 0) {
            noDataMessage.removeClass('d-none');
            
            let message = 'Tidak ada data bootcamp.';
            if (!currentUserIsAdmin) {
              message = 'Belum ada bootcamp yang Anda buat.';
            }
            if (search) {
              message = `Tidak ditemukan bootcamp untuk pencarian "${search}"`;
            }
            if (category) {
              const categoryName = $('#categoryFilter option:selected').text();
              message = `Tidak ada bootcamp dalam kategori "${categoryName}"`;
            }
            
            noDataMessage.find('h5').text(message);
            $('#total-count').text('0 bootcamp');
            return;
          }

          // Update total count
          const total = Number(res.total || 0);
          $('#total-count').text(`${total} bootcamp`);
          
          // Show search info if applicable
          if (search || category) {
            let infoText = '';
            if (search) infoText += `Pencarian: "${search}" `;
            if (category) {
              const categoryName = $('#categoryFilter option:selected').text();
              infoText += `Kategori: "${categoryName}" `;
            }
            if (infoText) {
              container.before(`
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                  <i class="fas fa-info-circle me-2"></i>
                  Menampilkan ${res.data.length} dari ${total} bootcamp ${infoText}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              `);
            }
          }

          // Render cards
          res.data.forEach(bc => container.append(renderBootcampCard(bc, currentUserIsAdmin)));

          // Pagination
          const totalPages = Math.max(1, Math.ceil(total / limit));
          
          if (totalPages > 1) {
            let html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

            // Previous button
            html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                      <a class="page-link" href="javascript:void(0)" onclick="loadBootcamps(${page - 1}, '${encodeURIComponent(search)}', '${category}', ${limit})">
                        <i class="fas fa-chevron-left"></i>
                      </a>
                    </li>`;

            // Page numbers
            const startPage = Math.max(1, page - 2);
            const endPage = Math.min(totalPages, startPage + 4);

            for (let i = startPage; i <= endPage; i++) {
              const active = i === page ? 'active' : '';
              html += `<li class="page-item ${active}">
                        <a class="page-link" href="javascript:void(0)" onclick="loadBootcamps(${i}, '${encodeURIComponent(search)}', '${category}', ${limit})">${i}</a>
                      </li>`;
            }

            // Next button
            html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">
                      <a class="page-link" href="javascript:void(0)" onclick="loadBootcamps(${page + 1}, '${encodeURIComponent(search)}', '${category}', ${limit})">
                        <i class="fas fa-chevron-right"></i>
                      </a>
                    </li>`;

            html += '</ul></nav>';
            pagination.html(html);
          } else {
            pagination.html('<small class="text-muted">Menampilkan semua data</small>');
          }
          
          currentPage = page;
          currentSearch = search;
          currentCategory = category;
        },
        error: function(xhr, status, err) {
          console.error('Error loading bootcamps:', status, err);
          container.html(`
            <div class="col-12 text-center py-5">
              <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
              <h5 class="text-danger">Gagal memuat data</h5>
              <p class="text-muted">Terjadi kesalahan saat memuat data bootcamp</p>
              <button class="btn btn-sm btn-primary" onclick="loadBootcamps()">
                <i class="fas fa-redo me-1"></i> Coba Lagi
              </button>
            </div>
          `);
        }
      });
    }

    // Add bootcamp
    $("#form-add-bootcamp").on("submit", function(e) {
      e.preventDefault();

      const selectedCategories = $('#form-add-bootcamp select').val() || [];
      const formData = new FormData(this);
      formData.append('categories', JSON.stringify(selectedCategories));

      // Show loading
      const submitBtn = $(this).find('button[type="submit"]');
      const originalText = submitBtn.html();
      submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
      submitBtn.prop('disabled', true);

      $.ajax({
        url: BASE_URL + "/bootcamp/create",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          if (res.success) {
            showAlert(res.message, "success");
            $("#modal-add-bootcamp").modal("hide");
            $("#form-add-bootcamp")[0].reset();
            $('.categories-select').val(null).trigger('change');
            loadBootcamps(currentPage, currentSearch, currentCategory);
          } else {
            showAlert(res.message, "error");
          }
        },
        error: function(xhr, status, error) {
          console.error('Error adding bootcamp:', error);
          showAlert('Terjadi kesalahan saat menambah bootcamp', 'error');
        },
        complete: function() {
          submitBtn.html(originalText);
          submitBtn.prop('disabled', false);
        }
      });
    });

    // Update bootcamp
    $("#form-edit-bootcamp").on("submit", function(e) {
      e.preventDefault();

      const selectedCategories = $('#edit-categories').val() || [];
      const formData = new FormData(this);
      formData.append('categories', JSON.stringify(selectedCategories));

      // Show loading
      const submitBtn = $(this).find('button[type="submit"]');
      const originalText = submitBtn.html();
      submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
      submitBtn.prop('disabled', true);

      $.ajax({
        url: BASE_URL + "/bootcamp/update",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          if (res.success) {
            showAlert(res.message, "success");
            $("#modal-edit-bootcamp").modal("hide");
            loadBootcamps(currentPage, currentSearch, currentCategory);
          } else {
            showAlert(res.message, "error");
          }
        },
        error: function(xhr, status, error) {
          console.error('Error updating bootcamp:', error);
          showAlert('Terjadi kesalahan saat memperbarui bootcamp', 'error');
        },
        complete: function() {
          submitBtn.html(originalText);
          submitBtn.prop('disabled', false);
        }
      });
    });

    // Search dengan debounce
    let _searchTimeout = null;
    $('#searchBootcamp').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        currentSearch = q;
        loadBootcamps(1, q, currentCategory, DEFAULT_LIMIT);
      }, 500);
    });

    // Category filter change
    $('#categoryFilter').on('change', function() {
      const categoryId = $(this).val();
      currentCategory = categoryId;
      loadBootcamps(1, currentSearch, categoryId, DEFAULT_LIMIT);
    });

    // Clear filters
    window.clearFilters = function() {
      $('#searchBootcamp').val('');
      $('#categoryFilter').val('');
      currentSearch = '';
      currentCategory = '';
      loadBootcamps(1, '', '', DEFAULT_LIMIT);
    };

    // Init 
    $(document).ready(function() {
      // Load categories
      loadCategories();

      // Initial load bootcamps
      loadBootcamps(1, '', '', DEFAULT_LIMIT);

      // Reset form when modal is closed
      $('#modal-add-bootcamp').on('hidden.bs.modal', function() {
        $('#form-add-bootcamp')[0].reset();
        $('.categories-select').val(null).trigger('change');
      });

      // Initialize edit modal
      $('#modal-edit-bootcamp').on('shown.bs.modal', function() {
        // Initialize Select2 for edit modal
        const $select = $('#edit-categories');
        if (!$select.hasClass('select2-hidden-accessible')) {
          $select.select2({
            dropdownParent: $('#modal-edit-bootcamp'),
            placeholder: "Pilih kategori...",
            allowClear: true
          });
        }
      });

      $('#modal-edit-bootcamp').on('hidden.bs.modal', function() {
        // Reset form
        $('#form-edit-bootcamp')[0].reset();
        const $select = $('#edit-categories');
        if ($select.hasClass('select2-hidden-accessible')) {
          $select.select2('destroy');
        }
        $select.empty();
      });
    });
  </script>

  <!-- Core JS Files -->
  <script src="/cms_ivss/public/assets/js/core/popper.min.js"></script>
  <script src="/cms_ivss/public/assets/js/core/bootstrap.min.js"></script>
  <script src="/cms_ivss/public/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/cms_ivss/public/assets/js/plugins/smooth-scrollbar.min.js"></script>

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script src="/cms_ivss/public/assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>