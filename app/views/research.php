<?php
$page_title = "Penelitian";
$page_breadcrumb = ["Pages", "Penelitian"];
?>
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
    /* Pastikan modal backdrop tidak mengganggu */
    .modal-backdrop {
      z-index: 1040 !important;
    }

    .modal {
      z-index: 1050 !important;
    }

    /* Pastikan Select2 dropdown muncul di atas modal */
    .select2-container {
      z-index: 1055 !important;
    }

    /* Tambahkan ini jika Select2 dropdown tidak terlihat */
    .select2-dropdown {
      z-index: 1060 !important;
    }

    .penelitian-card {
      transition: all 0.3s ease;
    }

    .penelitian-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .badge-sm {
      padding: 0.25em 0.6em;
      font-size: 0.75em;
    }
  </style>
</head>

<body class="g-sidenav-show  bg-gray-100">

  <?php include('includes/sidebar.php') ?>

  <main class="main-content position-relative border-radius-lg ">

    <?php include('includes/navbar.php') ?>

    <!-- Penelitian -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <!-- <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="m-0">Manajemen Penelitian</h5>
              <button class="btn btn-sm btn-success" id="btn-add-penelitian" data-bs-toggle="modal" data-bs-target="#modal-add-penelitian">
                <i class="fa fa-plus me-1"></i> Tambah Penelitian
              </button>
            </div>
          </div> -->

          <div class="card-body">
            <!-- Filter dan Search -->
            <!-- <div class="mb-4 d-flex justify-content-between gap-3 flex-wrap">
              <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari penelitian..." id="searchPenelitian">
              </div>
              <select id="categoryFilter" class="form-control" style="max-width: 200px;">
                <option value="">Semua Kategori</option> -->
                <!-- Options akan diisi oleh JavaScript -->
              <!-- </select>
              <select id="statusFilter" class="form-control" style="max-width: 200px;">
                <option value="">Semua Status</option>
                <option value="ongoing">Berjalan</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Dibatalkan</option>
                <option value="planned">Direncanakan</option>
              </select>
            </div> -->

            <div class="mb-3 d-flex justify-content-between gap-3"> 
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari berita..." id="searchPenelitian">
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
                <i class="fa fa-plus"></i> Tambah Penilitian
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
          <div class="mb-3">
            <label class="form-label">Judul Penelitian <span class="text-danger">*</span></label>
            <input name="title" type="text" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
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
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                <input name="start_date" type="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tanggal Selesai</label>
                <input name="finish_date" type="date" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi penelitian..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="categories[]" class="form-control categories-select" multiple style="width: 100%;">
              <!-- Data diisi JavaScript -->
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Peneliti</label>
            <select name="participants[]" class="form-control participants-select" multiple style="width: 100%;">
              <!-- Data diisi JavaScript -->
            </select>
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
          <div class="mb-3">
            <label class="form-label">Judul Penelitian <span class="text-danger">*</span></label>
            <input name="title" id="edit-title" type="text" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
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
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                <input name="start_date" id="edit-start_date" type="date" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tanggal Selesai</label>
                <input name="finish_date" id="edit-finish_date" type="date" class="form-control">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" id="edit-description" class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="categories[]" id="edit-categories" class="form-control categories-select" multiple style="width: 100%;">
              <!-- Options akan diisi oleh JavaScript -->
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Peneliti</label>
            <select name="participants[]" id="edit-participants" class="form-control participants-select" multiple style="width: 100%;">
              <!-- Options akan diisi oleh JavaScript -->
            </select>
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
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Status:</strong>
              <span id="detail-status" class="ms-2 badge"></span>
            </div>
            <div class="col-md-6">
              <strong>Anggaran:</strong>
              <span id="detail-budget" class="ms-2"></span>
            </div>
          </div>
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
          <div class="mb-3">
            <strong>Dibuat oleh:</strong>
            <span id="detail-creator" class="ms-2"></span>
          </div>
          <div class="mb-3">
            <strong>Kategori:</strong>
            <div id="detail-categories" class="mt-1"></div>
          </div>
          <div class="mb-3">
            <strong>Peneliti:</strong>
            <div id="detail-participants" class="mt-1"></div>
          </div>
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
      }, 1000);
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

    // Show edit penelitian - VERSI PERBAIKAN
    window.showEditPenelitian = function(el) {
      const $el = $(el);
      const id = $el.data('id');
      
      console.log('Edit button clicked, ID:', id);
      console.log('Data attributes:', {
        title: $el.data('title'),
        status: $el.data('status'),
        start_date: $el.data('start_date'),
        finish_date: $el.data('finish_date'),
        budget: $el.data('budget'),
        categories: $el.data('categories'),
        participants: $el.data('participants')
      });

      // Ambil data dari atribut data
      const title = $el.data('title') || '';
      const status = $el.data('status') || 'ongoing';
      const description = $el.data('description') || '';
      const budget = $el.data('budget') || 0;
      const start_date = $el.data('start_date') || '';
      const finish_date = $el.data('finish_date') || '';
      
      // Parse kategori dan peserta
      let selectedCategoryIds = [];
      const categoryIdsData = $el.data('categories');
      
      if (categoryIdsData) {
        try {
          if (typeof categoryIdsData === 'string') {
            selectedCategoryIds = JSON.parse(categoryIdsData);
          } else if (Array.isArray(categoryIdsData)) {
            selectedCategoryIds = categoryIdsData;
          }
        } catch (e) {
          console.error('Error parsing categories:', e);
          selectedCategoryIds = [];
        }
      }
      
      let selectedParticipantIds = [];
      const participantIdsData = $el.data('participants');
      
      if (participantIdsData) {
        try {
          if (typeof participantIdsData === 'string') {
            selectedParticipantIds = JSON.parse(participantIdsData);
          } else if (Array.isArray(participantIdsData)) {
            selectedParticipantIds = participantIdsData;
          }
        } catch (e) {
          console.error('Error parsing participants:', e);
          selectedParticipantIds = [];
        }
      }

      // Isi input form dengan data yang ada
      $('#edit-id').val(id);
      $('#edit-title').val(title);
      $('#edit-status').val(status);
      $('#edit-description').val(description);
      $('#edit-budget').val(budget);
      $('#edit-start_date').val(start_date);
      $('#edit-finish_date').val(finish_date);

      console.log('Form values set:', {
        status: status,
        start_date: start_date,
        finish_date: finish_date,
        categories: selectedCategoryIds,
        participants: selectedParticipantIds
      });

      // Simpan data untuk inisialisasi Select2 nanti
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
          console.log('Detail response:', res);
          if (res.success && res.data) {
            const penelitian = res.data;

            $('#detail-title').text(penelitian.title);
            $('#detail-description').text(penelitian.description || 'Tidak ada deskripsi');
            $('#detail-creator').text(penelitian.creator_name || 'Admin');
            $('#detail-budget').text(formatCurrency(penelitian.budget));
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
      
      console.log('Initializing Select2 with:', {
        categories: selectedCategories,
        participants: selectedParticipants
      });

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
        console.log('Categories set to:', selectedCategories);
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
        console.log('Participants set to:', selectedParticipants);
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
          console.log("Users response:", res);
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
                    text: u.name
                  })),
                  tags: false
                });
              }
            });
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

      const categoriesHtml = penelitian.categories && penelitian.categories !== '' ?
        penelitian.categories.split(', ').map(cat =>
          `<span class="badge bg-gradient-info text-white me-1 mb-1 px-2 py-1 badge-sm">${cat}</span>`
        ).join('') :
        '<span class="text-muted text-xs">Tidak ada kategori</span>';

      const participantsHtml = penelitian.participants && penelitian.participants !== '' ?
        penelitian.participants.split(', ').map(part =>
          `<span class="badge bg-gradient-warning text-white me-1 mb-1 px-2 py-1 badge-sm">${part}</span>`
        ).join('') :
        '<span class="text-muted text-xs">Tidak ada peneliti</span>';

      const statusBadge = {
        'ongoing': 'bg-gradient-info',
        'completed': 'bg-gradient-success',
        'cancelled': 'bg-gradient-danger',
        'planned': 'bg-gradient-warning'
      } [penelitian.status] || 'bg-gradient-secondary';

      // Pastikan data yang diperlukan tersedia
      const categoryIds = Array.isArray(penelitian.category_ids) ? penelitian.category_ids : [];
      const participantIds = Array.isArray(penelitian.participant_ids) ? penelitian.participant_ids : [];

      // Format tanggal untuk data-attribute (pastikan format YYYY-MM-DD)
      const startDate = penelitian.start_date ? penelitian.start_date.split(' ')[0] : '';
      const finishDate = penelitian.finish_date ? penelitian.finish_date.split(' ')[0] : '';

      // Escape data untuk HTML attribute
      const escapeForAttribute = (str) => {
        if (!str) return '';
        return String(str)
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;')
          .replace(/&/g, '&amp;');
      };

      console.log('Penelitian id : ' + penelitian.id);

      return `
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
          <div class="card border-0 shadow-xs h-100 rounded-4 penelitian-card">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0 text-dark fw-bold text-sm">${penelitian.title}</h6>
                <span class="badge ${statusBadge} text-white badge-sm">${getStatusText(penelitian.status)}</span>
              </div>
              
              <div class="mb-3">
                <p class="text-muted text-xs mb-2">${shortDescription}</p>
              </div>

              <div class="mb-3">
                <div class="text-xs text-muted mb-1"><strong>Kategori:</strong></div>
                <div class="d-flex flex-wrap">
                  ${categoriesHtml}
                </div>
              </div>

              <div class="mb-3">
                <div class="text-xs text-muted mb-1"><strong>Peneliti:</strong></div>
                <div class="d-flex flex-wrap">
                  ${participantsHtml}
                </div>
              </div>

              <div class="row text-xs text-muted mb-3">
                <div class="col-6">
                  <i class="fas fa-calendar me-1"></i>
                  ${formatDate(penelitian.start_date)}
                </div>
                <div class="col-6 text-end">
                  <i class="fas fa-money-bill me-1"></i>
                  ${formatCurrency(penelitian.budget)}
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-sm btn-outline-info" onclick="showDetailPenelitian(${penelitian.id})">
                  <i class="fas fa-eye me-1"></i> Detail
                </button>
                <div class="d-flex gap-1">
                  <button class="btn btn-sm btn-warning" 
                    onclick="showEditPenelitian(this)"
                    data-id="${penelitian.id}"
                    data-title="${escapeForAttribute(penelitian.title)}"
                    data-status="${escapeForAttribute(penelitian.status)}"
                    data-description="${escapeForAttribute(penelitian.description)}"
                    data-budget="${escapeForAttribute(penelitian.budget)}"
                    data-start_date="${escapeForAttribute(startDate)}"
                    data-finish_date="${escapeForAttribute(finishDate)}"
                    data-categories='${JSON.stringify(categoryIds)}'
                    data-participants='${JSON.stringify(participantIds)}'>
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" onclick="deletePenelitian(${penelitian.id})">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // Load penelitian
    function loadPenelitian(page = 1, search = '', category = '', status = '', limit = DEFAULT_LIMIT) {
      const container = $('#penelitianContainer');
      const pagination = $('#pagination');
      container.html(`<div class="col-12 text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</div>`);
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
          console.log('Penelitian response:', res);
          container.empty();
          
          if (!res || !res.success || !res.data || res.data.length === 0) {
            let message = 'Tidak ada data penelitian.';
            if (search) message += ` untuk pencarian "${search}"`;
            if (category) {
              const categoryName = $('#categoryFilter option:selected').text();
              message += ` dalam kategori "${categoryName}"`;
            }
            if (status) {
              const statusName = $('#statusFilter option:selected').text();
              message += ` dengan status "${statusName}"`;
            }
            container.append(`<div class="col-12 text-center text-muted py-5"><i class="fas fa-inbox me-2"></i>${message}</div>`);
            return;
          }

          res.data.forEach(p => container.append(renderPenelitianCard(p)));

          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';

          if (page > 1) {
            html += `<button class="btn btn-sm btn-outline-primary mx-1" onclick="loadPenelitian(${page - 1}, '${encodeURIComponent(search)}', '${category}', '${status}', ${limit})">
                      <i class="fas fa-chevron-left"></i>
                    </button>`;
          }

          const startPage = Math.max(1, page - 2);
          const endPage = Math.min(totalPages, startPage + 4);

          for (let i = startPage; i <= endPage; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 ${cls} mx-1" onclick="loadPenelitian(${i}, '${encodeURIComponent(search)}', '${category}', '${status}', ${limit})">${i}</button>`;
          }

          if (page < totalPages) {
            html += `<button class="btn btn-sm btn-outline-primary mx-1" onclick="loadPenelitian(${page + 1}, '${encodeURIComponent(search)}', '${category}', '${status}', ${limit})">
                      <i class="fas fa-chevron-right"></i>
                    </button>`;
          }

          pagination.html(html);
          currentPage = page;
          currentSearch = search;
          currentCategory = category;
          currentStatus = status;
        },
        error: function(xhr, status, err) {
          console.error('Error loading penelitian:', status, err);
          container.html(`<div class="col-12 text-center text-danger py-5"><i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat data.</div>`);
        }
      });
    }

    // Add penelitian
    $("#form-add-penelitian").on("submit", function(e) {
      e.preventDefault();

      const selectedCategories = $('#form-add-penelitian select[name="categories[]"]').val() || [];
      const selectedParticipants = $('#form-add-penelitian select[name="participants[]"]').val() || [];

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
          showAlert('Terjadi kesalahan saat menambah penelitian', 'error');
        }
      });
    });

    // Update penelitian
    // Update penelitian
$("#form-edit-penelitian").on("submit", function(e) {
    e.preventDefault();
    
    // Debug form data
    console.log('=== FORM EDIT SUBMIT ===');
    const formData = $(this).serializeArray();
    console.log('Form Data:', formData);
    
    const selectedCategories = $('#edit-categories').val() || [];
    const selectedParticipants = $('#edit-participants').val() || [];
    
    console.log('Selected Categories:', selectedCategories);
    console.log('Selected Participants:', selectedParticipants);
    
    // Tambahkan categories dan participants ke formData
    formData.push({
        name: 'categories',
        value: JSON.stringify(selectedCategories)
    });
    formData.push({
        name: 'participants',
        value: JSON.stringify(selectedParticipants)
    });
    
    console.log('Final Data to Send:', $.param(formData));
    
    // Cek apakah ID ada
    const researchId = $('#edit-id').val();
    if (!researchId) {
        console.error('Research ID is missing!');
        showAlert('ID penelitian tidak ditemukan', 'error');
        return;
    }
    
    $.ajax({
        url: BASE_URL + "/penelitian/update",
        method: "POST",
        data: $.param(formData),
        dataType: "json",
        success: function(res) {
            console.log('Update Response:', res);
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

    // Delete penelitian
    window.deletePenelitian = function(id) {
      showConfirm("Yakin ingin menghapus penelitian ini?", function(ok) {
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

    // Init 
    $(document).ready(function() {
      loadCategoriesAndUsers();
      loadPenelitian(1, '', '', '', DEFAULT_LIMIT);

      // Event handler untuk modal edit ketika ditampilkan
      $('#modal-edit-penelitian').on('show.bs.modal', function() {
        // Tunggu sebentar untuk memastikan modal sudah siap
        setTimeout(initializeEditSelect2, 100);
      });

      $('#modal-add-penelitian').on('hidden.bs.modal', function() {
        $('#form-add-penelitian')[0].reset();
        $('.categories-select').val(null).trigger('change');
        $('.participants-select').val(null).trigger('change');
      });

      $('#modal-edit-penelitian').on('hidden.bs.modal', function() {
        // Reset data stored
        $(this).removeData('selectedCategories');
        $(this).removeData('selectedParticipants');
        
        // Reset form
        $('#form-edit-penelitian')[0].reset();
        
        // Destroy Select2 instances jika masih ada
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