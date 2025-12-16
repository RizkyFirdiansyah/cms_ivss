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
</head>

<body class="g-sidenav-show  bg-gray-100">

  <?php include('includes/sidebar.php') ?>

  <main class="main-content position-relative border-radius-lg ">

    <?php include('includes/navbar.php') ?>

    <!-- Penelitian -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Penelitian</h5>
          </div>
          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari penelitian..." id="searchPenelitian">
              </div>
              <select id="categoryFilter" class="form-control" style="max-width: 200px;">
                <option value="">Semua Kategori</option>
              </select>
              <select id="statusFilter" class="form-control" style="max-width: 200px;">
                <option value="">Semua Status</option>
                <option value="ongoing">Berjalan</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
                <option value="planned">Direncanakan</option>
              </select>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-news" data-bs-toggle="modal" data-bs-target="#modal-add-penelitian">
                <i class="fa fa-plus"></i> Tambah Penelitian
              </button>
            </div>

            <!-- Penelitian Cards -->
            <div class="row g-4" id="penelitianContainer">
              <div class="col-12 text-center text-muted py-5">
                <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
              </div>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="d-flex gap-1 justify-content-center mt-4"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Penelitian -->
  </main>

  <!-- Modal: Tambah Penelitian -->
  <div class="modal fade" id="modal-add-penelitian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form id="form-add-penelitian" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Penelitian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="add-penelitian-alert"></div>

          <!-- Judul -->
          <div class="mb-3">
            <label class="form-label">Judul Penelitian</label>
            <input name="title" type="text" class="form-control" required>
          </div>

          <!-- Status & Anggaran -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                  <option value="ongoing">Berjalan</option>
                  <option value="completed">Selesai</option>
                  <option value="cancelled">Dibatalkan</option>
                  <option value="planned">Direncanakan</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Anggaran</label>
                <input name="budget" type="number" class="form-control" min="0" value="0">
              </div>
            </div>
          </div>

          <!-- Max Participants & Dates -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Maks. Peserta</label>
                <input name="max_participants" type="number" class="form-control" min="1" value="10" id="add-max-participants">
                <small class="text-muted">Default: 10 peserta</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tanggal Mulai</label>
                <input name="start_date" type="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
              </div>
            </div>
          </div>

          <!-- Finish Date & Empty Column -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tanggal Selesai</label>
                <input name="finish_date" type="date" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <!-- Empty column for alignment -->
            </div>
          </div>

          <!-- Deskripsi -->
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi penelitian..."></textarea>
          </div>

          <!-- Kategori & Peneliti -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="categories[]" class="form-control categories-select" multiple style="width: 100%;"></select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Peneliti</label>
                <select name="participants[]" class="form-control participants-select" multiple style="width: 100%;"></select>
                <small class="text-muted">Pilih dosen/mahasiswa yang akan berpartisipasi</small>
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

  <!-- Modal: Edit Penelitian -->
  <div class="modal fade" id="modal-edit-penelitian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form id="form-edit-penelitian" class="modal-content">
        <input type="hidden" name="id" id="edit-id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Penelitian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="edit-penelitian-alert"></div>

          <!-- Judul -->
          <div class="mb-3">
            <label class="form-label">Judul Penelitian</label>
            <input name="title" id="edit-title" type="text" class="form-control" required>
          </div>

          <!-- Status & Anggaran -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" id="edit-status" class="form-control" required>
                  <option value="ongoing">Berjalan</option>
                  <option value="completed">Selesai</option>
                  <option value="cancelled">Dibatalkan</option>
                  <option value="planned">Direncanakan</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Anggaran</label>
                <input name="budget" id="edit-budget" type="number" class="form-control" min="0">
              </div>
            </div>
          </div>

          <!-- Max Participants & Dates -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Maks. Peserta</label>
                <input name="max_participants" type="number" class="form-control" min="1" id="edit-max-participants">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tanggal Mulai</label>
                <input name="start_date" id="edit-start_date" type="date" class="form-control" required>
              </div>
            </div>
          </div>

          <!-- Finish Date & Empty Column -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tanggal Selesai</label>
                <input name="finish_date" id="edit-finish_date" type="date" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <!-- Empty column for alignment -->
            </div>
          </div>

          <!-- Deskripsi -->
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" id="edit-description" class="form-control" rows="3"></textarea>
          </div>

          <!-- Kategori & Peneliti -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="categories[]" id="edit-categories" class="form-control categories-select" multiple style="width: 100%;"></select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Peneliti</label>
                <select name="participants[]" id="edit-participants" class="form-control participants-select" multiple style="width: 100%;"></select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Detail Penelitian -->
  <div class="modal fade" id="modal-detail-penelitian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Penelitian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <h4 id="detail-title" class="text-primary"></h4>
          </div>

          <!-- Status, Budget, Max Participants -->
          <div class="row mb-3">
            <div class="col-md-4">
              <strong>Status:</strong>
              <span id="detail-status" class="ms-2 badge"></span>
            </div>
            <div class="col-md-4">
              <strong>Anggaran:</strong>
              <span id="detail-budget" class="ms-2"></span>
            </div>
            <div class="col-md-4">
              <strong>Maks. Peserta:</strong>
              <span id="detail-max-participants" class="ms-2"></span>
            </div>
          </div>

          <!-- Dates -->
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Tanggal Mulai:</strong>
              <span id="detail-start-date" class="ms-2"></span>
            </div>
            <div class="col-md-6">
              <strong>Tanggal Selesai:</strong>
              <span id="detail-finish-date" class="ms-2"></span>
            </div>
          </div>

          <!-- Creator -->
          <div class="mb-3">
            <strong>Dibuat oleh:</strong>
            <span id="detail-creator" class="ms-2"></span>
          </div>

          <!-- Categories -->
          <div class="mb-3">
            <strong>Kategori:</strong>
            <div id="detail-categories" class="mt-1"></div>
          </div>

          <!-- Participants -->
          <div class="mb-3">
            <strong>Peneliti:</strong>
            <div id="detail-participants" class="mt-1"></div>
          </div>

          <!-- Description -->
          <div class="mb-3">
            <strong>Deskripsi:</strong>
            <p id="detail-description" class="mt-2 p-3 bg-light rounded"></p>
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
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";
    const DEFAULT_LIMIT = 6;
    let currentPage = 1;
    let currentSearch = '';
    let currentCategory = '';
    let currentStatus = '';
    let globalCategories = [];
    let globalUsers = [];

    // Deteksi role user dari PHP session
    const userRole = "<?= isset($_SESSION['role']) ? $_SESSION['role'] : 'guest' ?>";
    const userId = <?= isset($_SESSION['id']) ? $_SESSION['id'] : 0 ?>;

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
      }, 1500);
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

    function truncateText(text, maxLength = 100) {
      if (!text) return '';
      if (text.length <= maxLength) return text;
      return text.substring(0, maxLength) + '...';
    }

    function formatDate(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    }

    function formatCurrency(amount) {
      if (!amount) return 'Rp 0';
      return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    }

    // Get status text in Indonesian
    function getStatusText(status) {
      const statusMap = {
        'ongoing': 'Berjalan',
        'completed': 'Selesai',
        'cancelled': 'Dibatalkan',
        'planned': 'Direncanakan'
      };
      return statusMap[status] || status;
    }

    // Show edit penelitian dengan permission check
    window.showEditPenelitian = function(el) {
      const $el = $(el);
      const id = $el.data('id');
      const createdBy = $el.data('created_by');
      const userRoleInResearch = $el.data('user_role') || 'participant';

      // Permission check
      if (userRole === 'mahasiswa') {
        showAlert('Maaf, mahasiswa tidak dapat mengedit penelitian', 'error');
        return;
      }

      if (userRole === 'dosen' && parseInt(createdBy) !== userId) {
        showAlert('Maaf, Anda hanya dapat mengedit penelitian yang Anda buat sendiri', 'error');
        return;
      }

      // Ambil data dari atribut data - GUNAKAN UNDERSCORE
      const title = $el.data('title') || '';
      const status = $el.data('status') || 'ongoing';
      const description = $el.data('description') || '';
      const budget = $el.data('budget') || 0;
      const maxParticipants = $el.data('max_participants') || 10; // UNDERSCORE
      const start_date = $el.data('start_date') || '';
      const finish_date = $el.data('finish_date') || '';

      // Parse kategori dan peserta
      let selectedCategoryIds = [];
      let selectedParticipantIds = [];

      try {
        const categoryIdsData = $el.data('categories');
        if (categoryIdsData) {
          selectedCategoryIds = typeof categoryIdsData === 'string' ?
            JSON.parse(categoryIdsData) : categoryIdsData;
        }

        const participantIdsData = $el.data('participants');
        if (participantIdsData) {
          selectedParticipantIds = typeof participantIdsData === 'string' ?
            JSON.parse(participantIdsData) : participantIdsData;
        }
      } catch (e) {
        console.error('Error parsing data:', e);
      }

      // Isi input form dengan data yang ada
      $('#edit-id').val(id);
      $('#edit-title').val(title);
      $('#edit-status').val(status);
      $('#edit-description').val(description);
      $('#edit-budget').val(budget);
      $('#edit-max-participants').val(maxParticipants);
      $('#edit-start_date').val(start_date);
      $('#edit-finish_date').val(finish_date);

      // Simpan data creator untuk validasi
      $('#edit-id').data('created_by', createdBy);

      // Simpan data untuk Select2
      const editModal = $('#modal-edit-penelitian');
      editModal.data('selectedCategories', selectedCategoryIds);
      editModal.data('selectedParticipants', selectedParticipantIds);

      // Tampilkan modal
      const modal = new bootstrap.Modal(editModal[0]);
      modal.show();
    };

    // Show detail penelitian
    window.showDetailPenelitian = function(id) {
      $.ajax({
        url: BASE_URL + '/penelitian/detail',
        method: 'GET',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            const penelitian = res.data;

            // Handle max participants
            const maxParticipants = penelitian.max_participants || 10;

            $('#detail-title').text(penelitian.title);
            $('#detail-description').text(penelitian.description || 'Tidak ada deskripsi');
            $('#detail-creator').text(penelitian.creator_name || 'Admin');
            $('#detail-budget').text(formatCurrency(penelitian.budget));
            $('#detail-max-participants').text(maxParticipants);
            $('#detail-start-date').text(formatDate(penelitian.start_date));
            $('#detail-finish-date').text(formatDate(penelitian.finish_date));

            // Status badge
            const statusBadge = {
              'ongoing': 'bg-gradient-info',
              'completed': 'bg-gradient-success',
              'cancelled': 'bg-gradient-danger',
              'planned': 'bg-gradient-warning'
            } [penelitian.status] || 'bg-gradient-secondary';

            $('#detail-status').text(getStatusText(penelitian.status)).attr('class', `badge ${statusBadge} text-white`);

            // Categories
            const categoriesHtml = penelitian.categories && penelitian.categories !== '' ?
              penelitian.categories.split(', ').map(cat =>
                `<span class="badge bg-gradient-info text-white me-1 mb-1 px-2 py-1 badge-sm">${cat}</span>`
              ).join('') :
              '<span class="text-muted">Tidak ada kategori</span>';
            $('#detail-categories').html(categoriesHtml);

            // Participants
            const participantsHtml = penelitian.participants && penelitian.participants !== '' ?
              penelitian.participants.split(', ').map(part =>
                `<span class="badge bg-gradient-warning text-white me-1 mb-1 px-2 py-1 badge-sm">${part}</span>`
              ).join('') :
              '<span class="text-muted">Tidak ada peneliti</span>';
            $('#detail-participants').html(participantsHtml);

            new bootstrap.Modal('#modal-detail-penelitian').show();
          } else {
            showAlert('Gagal memuat detail penelitian', 'error');
          }
        },
        error: function(xhr) {
          console.error('Error loading penelitian detail:', xhr);
          showAlert('Terjadi kesalahan saat memuat detail penelitian', 'error');
        }
      });
    };

    // Fungsi untuk inisialisasi Select2 pada modal edit
    function initializeEditSelect2() {
      const $modal = $('#modal-edit-penelitian');
      const selectedCategories = $modal.data('selectedCategories') || [];
      const selectedParticipants = $modal.data('selectedParticipants') || [];

      // Inisialisasi Select2 untuk kategori
      const $categorySelect = $('#edit-categories');
      if ($categorySelect.hasClass('select2-hidden-accessible')) {
        $categorySelect.select2('destroy');
      }

      $categorySelect.empty();
      globalCategories.forEach(cat => {
        const option = new Option(cat.name, cat.id, false, false);
        $categorySelect.append(option);
      });

      $categorySelect.select2({
        dropdownParent: $('#modal-edit-penelitian'),
        placeholder: "Pilih kategori...",
        allowClear: true,
        tags: false
      });

      // Set nilai yang dipilih untuk kategori
      if (selectedCategories.length > 0) {
        $categorySelect.val(selectedCategories).trigger('change');
      } else {
        $categorySelect.val(null).trigger('change');
      }

      // Inisialisasi Select2 untuk peneliti
      const $participantSelect = $('#edit-participants');
      if ($participantSelect.hasClass('select2-hidden-accessible')) {
        $participantSelect.select2('destroy');
      }

      $participantSelect.empty();
      globalUsers.forEach(user => {
        const option = new Option(user.name, user.id, false, false);
        $participantSelect.append(option);
      });

      $participantSelect.select2({
        dropdownParent: $('#modal-edit-penelitian'),
        placeholder: "Pilih peneliti...",
        allowClear: true,
        tags: false
      });

      // Set nilai yang dipilih untuk peneliti
      if (selectedParticipants.length > 0) {
        $participantSelect.val(selectedParticipants).trigger('change');
      } else {
        $participantSelect.val(null).trigger('change');
      }
    }

    // Load category list dan user list
    function loadCategoriesAndUsers() {
      // Load categories
      $.ajax({
        url: BASE_URL + '/penelitian/categories',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            globalCategories = res.data;

            $('.categories-select').each(function() {
              const $select = $(this);
              if (!$select.hasClass('select2-hidden-accessible')) {
                $select.select2({
                  dropdownParent: $select.closest('.modal'),
                  placeholder: 'Pilih kategori...',
                  allowClear: true,
                  data: globalCategories.map(c => ({
                    id: c.id,
                    text: c.name
                  })),
                  tags: false
                });
              }
            });

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

      // Load users untuk participants
      $.ajax({
        url: BASE_URL + '/penelitian/users',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            globalUsers = res.data;

            $('.participants-select').each(function() {
              const $select = $(this);
              if (!$select.hasClass('select2-hidden-accessible')) {
                $select.select2({
                  dropdownParent: $select.closest('.modal'),
                  placeholder: 'Pilih peneliti...',
                  allowClear: true,
                  data: globalUsers.map(u => ({
                    id: u.id,
                    text: u.name + ' (' + (u.role || 'user') + ')'
                  })),
                  tags: false
                });
              }
            });
          } else {
            console.warn('No users data received:', res);
            showAlert('Tidak ada pengguna yang tersedia untuk dipilih', 'warning');
          }
        },
        error: function(xhr) {
          console.error('Error loading users:', xhr);
          showAlert('Gagal memuat daftar peneliti', 'error');
        }
      });
    }

    // Render penelitian card
    function renderPenelitianCard(penelitian) {
      const shortDescription = truncateText(penelitian.description, 120);

      // Categories HTML
      const categoriesHtml = penelitian.categories && penelitian.categories !== '' ?
        penelitian.categories.split(', ').map(cat =>
          `<span class="badge bg-gradient-info text-white me-1 mb-1 px-2 py-1 badge-sm">${cat}</span>`
        ).join('') :
        '<span class="text-muted text-xs">Tidak ada kategori</span>';

      // Participants HTML
      const participantsHtml = penelitian.participants && penelitian.participants !== '' ?
        penelitian.participants.split(', ').map(part =>
          `<span class="badge bg-gradient-warning text-white me-1 mb-1 px-2 py-1 badge-sm">${part}</span>`
        ).join('') :
        '<span class="text-muted text-xs">Tidak ada peneliti</span>';

      // Status badge
      const statusBadge = {
        'ongoing': 'bg-gradient-info',
        'completed': 'bg-gradient-success',
        'cancelled': 'bg-gradient-danger',
        'planned': 'bg-gradient-warning'
      } [penelitian.status] || 'bg-gradient-secondary';

      // Data untuk form edit
      const categoryIds = Array.isArray(penelitian.category_ids) ? penelitian.category_ids : [];
      const participantIds = Array.isArray(penelitian.participant_ids) ? penelitian.participant_ids : [];
      const startDate = penelitian.start_date ? penelitian.start_date.split(' ')[0] : '';
      const finishDate = penelitian.finish_date ? penelitian.finish_date.split(' ')[0] : '';
      const maxParticipants = penelitian.max_participants || 10;

      // Escape data untuk HTML attribute
      const escapeForAttribute = (str) => {
        if (!str) return '';
        return String(str)
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;')
          .replace(/&/g, '&amp;');
      };

      // Tentukan role user dalam penelitian ini
      const isCreator = parseInt(penelitian.created_by_user_id) === userId;
      const userRoleInResearch = penelitian.user_role_in_research || (isCreator ? 'creator' : 'participant');

      // Role badge
      let roleBadge = '';
      if (userRoleInResearch === 'creator') {
        roleBadge = `<span class="badge bg-gradient-success  text-white badge-sm ms-2">Pemilik</span>`;
      } else {
        roleBadge = `<span class="badge bg-gradient-warning text-white badge-sm ms-2">Peserta</span>`;
      }

      // Tentukan tombol aksi
      let actionButtons = '';

      if (userRole === 'mahasiswa') {
        // Mahasiswa hanya bisa lihat detail
        actionButtons = `
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-info" onclick="showDetailPenelitian(${penelitian.id})">
              <i class="fas fa-eye me-1"></i>
            </button>
          </div>
        `;
      } else if (userRole === 'dosen') {
        if (userRoleInResearch === 'creator') {
          // Dosen sebagai creator bisa edit/hapus
          actionButtons = `
            <div class="d-flex gap-2">
              <button class="btn mb-0 px-3 btn-info btn-sm text-xs" onclick="showDetailPenelitian(${penelitian.id})">
                <i class="fas fa-eye"></i>
              </button>
              <button class="btn mb-0 px-3 btn-warning btn-sm text-xs"
                onclick="showEditPenelitian(this)"
                data-id="${penelitian.id}"
                data-title="${escapeForAttribute(penelitian.title)}"
                data-status="${escapeForAttribute(penelitian.status)}"
                data-description="${escapeForAttribute(penelitian.description)}"
                data-budget="${escapeForAttribute(penelitian.budget)}"
                data-max_participants="${escapeForAttribute(maxParticipants)}"
                data-start_date="${escapeForAttribute(startDate)}"
                data-finish_date="${escapeForAttribute(finishDate)}"
                data-categories='${JSON.stringify(categoryIds)}'
                data-participants='${JSON.stringify(participantIds)}'
                data-created_by="${penelitian.created_by_user_id}"
                data-user_role="${userRoleInResearch}">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn mb-0 px-3 btn-danger btn-sm text-xs" onclick="deletePenelitian(${penelitian.id})">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          `;
        } else {
          // Dosen sebagai participant hanya lihat
          actionButtons = `
            <div class="d-flex gap-2">
              <button class="btn mb-0 px-3 btn-info btn-sm text-xs" onclick="showDetailPenelitian(${penelitian.id})">
                <i class="fas fa-eye me-1"></i>
              </button>
            </div>
          `;
        }
      } else {
        // Kepala lab / admin full access
        actionButtons = `
          <div class="d-flex gap-2">
            <button class="btn mb-0 px-3 btn-info btn-sm text-xs" onclick="showDetailPenelitian(${penelitian.id})">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn mb-0 px-3 btn-warning btn-sm text-xs"
              onclick="showEditPenelitian(this)"
              data-id="${penelitian.id}"
              data-title="${escapeForAttribute(penelitian.title)}"
              data-status="${escapeForAttribute(penelitian.status)}"
              data-description="${escapeForAttribute(penelitian.description)}"
              data-budget="${escapeForAttribute(penelitian.budget)}"
              data-max_participants="${escapeForAttribute(maxParticipants)}"
              data-start_date="${escapeForAttribute(startDate)}"
              data-finish_date="${escapeForAttribute(finishDate)}"
              data-categories='${JSON.stringify(categoryIds)}'
              data-participants='${JSON.stringify(participantIds)}'
              data-created_by="${penelitian.created_by_user_id}"
              data-user_role="${userRoleInResearch}">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn mb-0 px-3 btn-danger btn-sm text-xs" onclick="deletePenelitian(${penelitian.id})">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        `;
      }

      return `
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
          <div class="card border-0 shadow-xs h-100 rounded-4 penelitian-card">
            <div class="card-body p-3 d-flex flex-column">
              <!-- Judul & Status & Role -->
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h6 class="mb-0 text-dark fw-bold text-sm">
                    ${penelitian.title}
                  </h6>
                  <small class="text-xs text-muted">
                    <i class="fas fa-user me-1"></i>
                    ${penelitian.creator_name || 'Tidak diketahui'}
                  </small>
                </div>
                <div class="d-flex flex-column align-items-end">
                  <span class="badge ${statusBadge} text-white badge-sm mb-1">
                    ${getStatusText(penelitian.status)}
                  </span>
                  ${roleBadge}
                </div>
              </div>
              
              <!-- Deskripsi -->
              <p class="text-muted text-xs mb-3">
                ${shortDescription}
              </p>
              
              <!-- Kategori -->
              <div class="mb-3">
                <div class="text-xs text-muted fw-semibold mb-1">Kategori:</div>
                <div class="d-flex flex-wrap">
                  ${categoriesHtml}
                </div>
              </div>
              
              <!-- Peneliti -->
              <div class="mb-3">
                <div class="text-xs text-muted fw-semibold mb-1">Peneliti:</div>
                <div class="d-flex flex-wrap">
                  ${participantsHtml}
                </div>
              </div>
              
              <!-- Informasi Utama -->
              <div class="row text-xs text-muted mb-3">
                <div class="col-4">
                  <i class="fas fa-calendar me-1"></i>
                  ${formatDate(penelitian.start_date)}
                </div>
                <div class="col-4 text-center">
                  <i class="fas fa-users me-1"></i>
                  ${maxParticipants}
                </div>
                <div class="col-4 text-end">
                  <i class="fas fa-money-bill me-1"></i>
                  ${formatCurrency(penelitian.budget)}
                </div>
              </div>
              
              <!-- Tombol Aksi -->
              <div class="mt-auto">
                ${actionButtons}
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // Load penelitian dengan role-based filtering
    function loadPenelitian(page = 1, search = '', category = '', status = '', limit = DEFAULT_LIMIT) {
      const container = $('#penelitianContainer');
      const pagination = $('#pagination');
      container.html(`<div class="col-12 text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</div>`);
      pagination.empty();

      const requestData = {
        page: page,
        limit: limit,
        search: search
      };

      if (category) {
        requestData.category_id = category;
      }

      if (status) {
        requestData.status = status;
      }

      $.ajax({
        url: BASE_URL + '/penelitian/list',
        method: 'GET',
        dataType: 'json',
        data: requestData,
        success: function(res) {
          container.empty();

          if (!res || !res.success || !res.data || res.data.length === 0) {
            let message = 'Tidak ada data penelitian.';
            let subMessage = '';

            if (userRole === 'dosen') {
              message = 'Belum ada penelitian.';
              subMessage = '<br><small class="text-sm">Anda belum membuat atau mengikuti penelitian</small>';
            } else if (userRole === 'mahasiswa') {
              message = 'Belum ada penelitian yang diikuti.';
              subMessage = '<br><small class="text-sm">Anda belum diundang mengikuti penelitian</small>';
            }

            if (search) message += ` untuk pencarian "${search}"`;
            if (category) {
              const categoryName = $('#categoryFilter option:selected').text();
              message += ` dalam kategori "${categoryName}"`;
            }
            if (status) {
              const statusName = $('#statusFilter option:selected').text();
              message += ` dengan status "${statusName}"`;
            }

            container.append(`
              <div class="col-12 text-center text-muted py-5">
                <i class="fas fa-inbox me-2"></i>
                ${message}
                ${subMessage}
              </div>
            `);
            return;
          }

          res.data.forEach(p => container.append(renderPenelitianCard(p)));

          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';

          const startPage = Math.max(1, page - 2);
          const endPage = Math.min(totalPages, startPage + 4);

          for (let i = startPage; i <= endPage; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 ${cls} mx-1" onclick="loadPenelitian(${i}, '${encodeURIComponent(search)}', '${category}', '${status}', ${limit})">${i}</button>`;
          }

          pagination.html(html);
          currentPage = page;
          currentSearch = search;
          currentCategory = category;
          currentStatus = status;
        },
        error: function(xhr, status, err) {
          console.error('Error loading penelitian:', status, err);
          container.html(`
            <div class="col-12 text-center text-danger py-5">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Gagal memuat data penelitian.
              <br><small class="text-muted">Silakan refresh halaman</small>
            </div>
          `);
        }
      });
    }

    // Add penelitian dengan validasi
    $("#form-add-penelitian").on("submit", function(e) {
      e.preventDefault();

      // Validasi role: mahasiswa tidak bisa buat research
      if (userRole === 'mahasiswa') {
        showAlert('Maaf, mahasiswa tidak dapat membuat penelitian', 'error');
        return;
      }

      const maxParticipants = parseInt($('#add-max-participants').val()) || 10;
      const selectedParticipants = $('select[name="participants[]"]').val() || [];
      const selectedCategories = $('select[name="categories[]"]').val() || [];
      const currentCount = selectedParticipants.length;

      // Validasi max participants
      if (currentCount > maxParticipants) {
        showAlert(`Jumlah peserta (${currentCount}) melebihi batas maksimum (${maxParticipants})`, 'error');
        return;
      }

      // Validasi: creator tidak bisa menambahkan diri sendiri
      if (selectedParticipants.includes(userId.toString())) {
        showAlert('Anda tidak dapat menambahkan diri sendiri sebagai peserta', 'error');
        return;
      }

      const formData = $(this).serializeArray();
      formData.push({
        name: 'categories',
        value: JSON.stringify(selectedCategories)
      });
      formData.push({
        name: 'participants',
        value: JSON.stringify(selectedParticipants)
      });

      $.ajax({
        url: BASE_URL + "/penelitian/create",
        method: "POST",
        data: $.param(formData),
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-add-penelitian").modal("hide");
            $("#form-add-penelitian")[0].reset();
            $('.categories-select').val(null).trigger('change');
            $('.participants-select').val(null).trigger('change');
            loadPenelitian(currentPage, currentSearch, currentCategory, currentStatus);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error adding penelitian:', error);
          console.error('XHR response:', xhr.responseText);
          showAlert('Terjadi kesalahan saat menambah penelitian', 'error');
        }
      });
    });

    // Update penelitian dengan validasi
    $("#form-edit-penelitian").on("submit", function(e) {
      e.preventDefault();

      const researchId = $('#edit-id').val();
      const createdBy = $('#edit-id').data('created_by');

      // Permission check
      if (userRole === 'mahasiswa') {
        showAlert('Maaf, mahasiswa tidak dapat mengedit penelitian', 'error');
        return;
      }

      if (userRole === 'dosen' && parseInt(createdBy) !== userId) {
        showAlert('Maaf, Anda hanya dapat mengedit penelitian yang Anda buat sendiri', 'error');
        return;
      }

      const maxParticipants = parseInt($('#edit-max-participants').val()) || 10;
      const selectedCategories = $('#edit-categories').val() || [];
      const selectedParticipants = $('#edit-participants').val() || [];
      const currentCount = selectedParticipants.length;

      // Validasi max participants
      if (currentCount > maxParticipants) {
        showAlert(`Jumlah peserta (${currentCount}) melebihi batas maksimum (${maxParticipants})`, 'error');
        return;
      }

      // Validasi: creator tidak bisa menambahkan diri sendiri
      if (selectedParticipants.includes(userId.toString())) {
        showAlert('Anda tidak dapat menambahkan diri sendiri sebagai peserta', 'error');
        return;
      }

      const formData = $(this).serializeArray();
      formData.push({
        name: 'categories',
        value: JSON.stringify(selectedCategories)
      });
      formData.push({
        name: 'participants',
        value: JSON.stringify(selectedParticipants)
      });

      if (!researchId) {
        showAlert('ID penelitian tidak ditemukan', 'error');
        return;
      }

      $.ajax({
        url: BASE_URL + "/penelitian/update",
        method: "POST",
        data: $.param(formData),
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-edit-penelitian").modal("hide");
            loadPenelitian(currentPage, currentSearch, currentCategory, currentStatus);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error updating research:', error);
          console.error('XHR Response:', xhr.responseText);
          showAlert('Terjadi kesalahan saat memperbarui penelitian', 'error');
        }
      });
    });

    // Delete penelitian dengan validasi
    window.deletePenelitian = function(id) {
      // Untuk mahasiswa, langsung block
      if (userRole === 'mahasiswa') {
        showAlert('Maaf, mahasiswa tidak dapat menghapus penelitian', 'error');
        return;
      }

      // Untuk dosen, cek apakah creator
      if (userRole === 'dosen') {
        // Ambil data penelitian untuk cek kepemilikan
        $.ajax({
          url: BASE_URL + '/penelitian/detail',
          method: 'GET',
          data: {
            id: id
          },
          dataType: 'json',
          success: function(res) {
            if (res.success && res.data) {
              const penelitian = res.data;

              // Cek apakah dosen adalah creator
              if (parseInt(penelitian.created_by_user_id) !== userId) {
                showAlert('Maaf, Anda hanya dapat menghapus penelitian yang Anda buat sendiri', 'error');
                return;
              }

              // Jika creator, tampilkan konfirmasi
              showConfirm("Yakin ingin menghapus penelitian ini?<br><small>Aksi ini tidak dapat dibatalkan</small>", function(ok) {
                if (!ok) return;

                $.ajax({
                  url: BASE_URL + "/penelitian/delete",
                  method: "POST",
                  data: {
                    id: id
                  },
                  dataType: "json",
                  success: function(res) {
                    showAlert(res.message, res.success ? "success" : "error");
                    if (res.success) loadPenelitian(currentPage, currentSearch, currentCategory, currentStatus);
                  },
                  error: function(xhr, status, error) {
                    console.error('Error deleting penelitian:', error);
                    showAlert('Terjadi kesalahan saat menghapus penelitian', 'error');
                  }
                });
              });
            } else {
              showAlert('Gagal memuat data penelitian', 'error');
            }
          },
          error: function(xhr) {
            console.error('Error loading research detail for delete:', xhr);
            showAlert('Terjadi kesalahan saat memverifikasi penelitian', 'error');
          }
        });
      } else {
        // Untuk kepala lab / admin, langsung konfirmasi
        showConfirm("Yakin ingin menghapus penelitian ini?<br><small>Aksi ini tidak dapat dibatalkan</small>", function(ok) {
          if (!ok) return;

          $.ajax({
            url: BASE_URL + "/penelitian/delete",
            method: "POST",
            data: {
              id: id
            },
            dataType: "json",
            success: function(res) {
              showAlert(res.message, res.success ? "success" : "error");
              if (res.success) loadPenelitian(currentPage, currentSearch, currentCategory, currentStatus);
            },
            error: function(xhr, status, error) {
              console.error('Error deleting penelitian:', error);
              showAlert('Terjadi kesalahan saat menghapus penelitian', 'error');
            }
          });
        });
      }
    };

    // Search dengan debounce
    let _searchTimeout = null;
    $('#searchPenelitian').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        currentSearch = q;
        loadPenelitian(1, q, currentCategory, currentStatus, DEFAULT_LIMIT);
      }, 300);
    });

    // Category filter change
    $('#categoryFilter').on('change', function() {
      const categoryId = $(this).val();
      currentCategory = categoryId;
      loadPenelitian(1, currentSearch, categoryId, currentStatus, DEFAULT_LIMIT);
    });

    // Status filter change
    $('#statusFilter').on('change', function() {
      const status = $(this).val();
      currentStatus = status;
      loadPenelitian(1, currentSearch, currentCategory, status, DEFAULT_LIMIT);
    });

    // Init dengan role-based UI
    $(document).ready(function() {
      // Customize UI berdasarkan role
      if (userRole === 'mahasiswa') {
        // Mahasiswa: hide tombol tambah, ubah judul
        $('#btn-add-news').remove();
        $('h5.m-0').text('Penelitian yang Diikuti');

        // Disable participants select (mahasiswa tidak bisa invite)
        $('.participants-select').prop('disabled', true).attr('title', 'Hanya pembuat penelitian yang dapat menambah peserta');

      } else if (userRole === 'dosen') {
        // Dosen: bisa buat penelitian
        $('h5.m-0').text('Penelitian');
      } else {
        // Kepala lab / admin
        $('h5.m-0').text('Penelitian - Semua Data');
      }

      loadCategoriesAndUsers();
      loadPenelitian(1, '', '', '', DEFAULT_LIMIT);

      // Event handler untuk modal edit
      $('#modal-edit-penelitian').on('show.bs.modal', function() {
        setTimeout(initializeEditSelect2, 100);

        // Untuk mahasiswa, disable form edit
        if (userRole === 'mahasiswa') {
          $(this).find('input, textarea, select').prop('disabled', true);
          $(this).find('.modal-footer').hide();
        }
      });

      $('#modal-add-penelitian').on('show.bs.modal', function() {
        // Untuk mahasiswa, hide modal tambah
        if (userRole === 'mahasiswa') {
          $(this).modal('hide');
          showAlert('Maaf, mahasiswa tidak dapat membuat penelitian', 'error');
          return false;
        }
      });

      $('#modal-add-penelitian').on('hidden.bs.modal', function() {
        $('#form-add-penelitian')[0].reset();
        $('.categories-select').val(null).trigger('change');
        $('.participants-select').val(null).trigger('change');
      });

      $('#modal-edit-penelitian').on('hidden.bs.modal', function() {
        $(this).removeData('selectedCategories');
        $(this).removeData('selectedParticipants');
        $('#form-edit-penelitian')[0].reset();

        // Destroy Select2
        const $categorySelect = $('#edit-categories');
        const $participantSelect = $('#edit-participants');

        if ($categorySelect.hasClass('select2-hidden-accessible')) {
          $categorySelect.select2('destroy');
        }

        if ($participantSelect.hasClass('select2-hidden-accessible')) {
          $participantSelect.select2('destroy');
        }
      });
    });
  </script>

  <!-- Core JS Files -->
  <script src="/cms_ivss/public/assets/js/core/popper.min.js"></script>
  <script src="/cms_ivss/public/assets/js/core/bootstrap.min.js"></script>
  <script src="/cms_ivss/public/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/cms_ivss/public/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="/cms_ivss/public/assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>