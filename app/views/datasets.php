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

    <!-- Datasets -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Manajemen Dataset</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari dataset..." id="searchDataset">
              </div>
              <select id="categoryFilter" class="form-control" style="max-width: 200px;">
                <option value="">Semua Dataset</option>
                <option value="my">Dataset Saya</option>
              </select>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-dataset" data-bs-toggle="modal" data-bs-target="#modal-add-dataset">
                <i class="fa fa-plus"></i> Tambah Dataset
              </button>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul Dataset</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Author</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Link</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dibuat</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Terakhir Diupdate</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                  </tr>
                </thead>
                <tbody id="datasetTableBody">
                  <tr>
                    <td colspan="6" class="text-center text-muted">Memuat data...</td>
                  </tr>
                </tbody>
              </table>
              <div id="pagination" class="d-flex gap-1 justify-content-center mt-3"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Datasets -->
  </main>

  <!-- Modal: Tambah Dataset -->
  <div class="modal fade" id="modal-add-dataset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-add-dataset" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Dataset</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="add-dataset-alert"></div>
          <div class="mb-3">
            <label class="form-label">Judul Dataset <span class="text-danger">*</span></label>
            <input name="title" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Link Dataset <span class="text-danger">*</span></label>
            <input name="link" type="url" class="form-control" placeholder="https://example.com/dataset" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal: Edit Dataset -->
  <div class="modal fade" id="modal-edit-dataset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-edit-dataset" class="modal-content">
        <input type="hidden" name="id" id="edit-id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Dataset</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="edit-dataset-alert"></div>
          <div class="mb-3">
            <label class="form-label">Judul Dataset <span class="text-danger">*</span></label>
            <input name="title" id="edit-title" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Link Dataset <span class="text-danger">*</span></label>
            <input name="link" id="edit-link" type="url" class="form-control" placeholder="https://example.com/dataset" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Author</label>
            <input type="text" id="edit-author" class="form-control" readonly>
          </div>
          <div class="alert alert-info text-sm">
            <i class="fa fa-info-circle me-2"></i>
            Hanya pemilik dataset dan kepala lab yang dapat mengedit data ini.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan Perubahan</button>
        </div>
      </form>
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
    const DEFAULT_LIMIT = 5;
    let currentPage = 1;
    let currentSearch = '';
    let currentFilter = '';
    let currentUserRole = '';
    let currentUserId = <?= $_SESSION['user_id'] ?? 0 ?>;

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

    // Show edit dataset
    window.showEditDataset = function(el) {
      const $el = $(el);
      const id = $el.data('id');
      const title = $el.data('title');
      const link = $el.data('link');
      const author = $el.data('author');

      // Isi form edit
      $('#edit-id').val(id);
      $('#edit-title').val(title);
      $('#edit-link').val(link);
      $('#edit-author').val(author);

      // Tampilkan modal
      new bootstrap.Modal('#modal-edit-dataset').show();
    };

    // Render dataset row
    function renderDatasetRow(dataset) {
      const createdDate = dataset.created_at ? new Date(dataset.created_at).toLocaleDateString('id-ID') : '-';
      const updatedDate = dataset.updated_at ? new Date(dataset.updated_at).toLocaleDateString('id-ID') : '-';

      // Tentukan apakah user bisa edit/hapus dataset ini
      // Hanya pemilik (user_id match) atau kepala lab yang bisa edit/hapus
      const isOwner = dataset.user_id === currentUserId;
      const canEditDelete = currentUserRole === 'kepala' || isOwner;

      return `
        <tr>
          <td>
            <div class="d-flex flex-column justify-content-center">
              <h6 class="mb-0 text-sm">${dataset.title}</h6>
              ${isOwner ? '<span class="text-xs text-success">Pemilik</span>' : ''}
            </div>
          </td>
          <td class="text-center text-sm align-middle">${dataset.author_name || '-'}</td>
          <td class="text-center align-middle">
            <a href="${dataset.link}" target="_blank" class="btn btn-sm bg-gradient-warning text-white px-3">
              <i class="fa fa-external-link me-1"></i>Link
            </a>
          </td>
          <td class="text-center text-sm align-middle">${createdDate}</td>
          <td class="text-center text-sm align-middle">${updatedDate}</td>
          <td class="text-center align-middle">
            ${canEditDelete ? `
              <button class="btn mb-0 px-3 btn-warning btn-sm text-xs" 
                onclick="showEditDataset(this)"
                data-id="${dataset.id}"
                data-title="${dataset.title ? dataset.title.replace(/"/g, '&quot;') : ''}"
                data-link="${dataset.link || ''}"
                data-author="${dataset.author_name || ''}">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn mb-0 px-3 btn-danger btn-sm text-xs" 
                onclick="deleteDataset(${dataset.id})">
                <i class="fa fa-trash"></i>
              </button>
            ` : `
              <span class="text-muted text-xs">Read Only</span>
            `}
          </td>
        </tr>
      `;
    }

    // Load datasets
    function loadDatasets(page = 1, search = '', filter = '', limit = DEFAULT_LIMIT) {
      const tbody = $('#datasetTableBody');
      const pagination = $('#pagination');
      tbody.html(`<tr><td colspan="6" class="text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>`);
      pagination.empty();

      const requestData = {
        page: page,
        limit: limit,
        search: search,
        filter: filter
      };

      $.ajax({
        url: BASE_URL + '/datasets/list',
        method: 'GET',
        dataType: 'json',
        data: requestData,
        success: function(res) {
          tbody.empty();

          // Simpan user role untuk akses kontrol
          if (res.current_user) {
            currentUserRole = res.current_user.role;
            currentUserId = res.current_user.id;
          }

          if (!res || !res.success || !res.data || res.data.length === 0) {
            let message = 'Tidak ada data dataset.';
            if (search) message += ` untuk pencarian "${search}"`;
            if (filter === 'my') message = 'Tidak ada dataset milik Anda.';
            tbody.append(`<tr><td colspan="6" class="text-center text-muted">${message}</td></tr>`);
            return;
          }

          res.data.forEach(d => tbody.append(renderDatasetRow(d)));

          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';

          // Page numbers
          const startPage = Math.max(1, page - 2);
          const endPage = Math.min(totalPages, startPage + 4);

          for (let i = startPage; i <= endPage; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 ${cls} mx-1" onclick="loadDatasets(${i}, '${encodeURIComponent(search)}', '${filter}', ${limit})">${i}</button>`;
          }

          pagination.html(html);
          currentPage = page;
          currentSearch = search;
          currentFilter = filter;
        },
        error: function(xhr, status, err) {
          console.error('Error loading datasets:', status, err);
          tbody.html(`<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>`);
        }
      });
    }

    // Add dataset
    $("#form-add-dataset").on("submit", function(e) {
      e.preventDefault();

      const formData = $(this).serializeArray();

      $.ajax({
        url: BASE_URL + "/datasets/create",
        method: "POST",
        data: $.param(formData),
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-add-dataset").modal("hide");
            $("#form-add-dataset")[0].reset();
            loadDatasets(currentPage, currentSearch, currentFilter);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error adding dataset:', error);
          showAlert('Terjadi kesalahan saat menambah dataset', 'error');
        }
      });
    });

    // Update dataset
    $("#form-edit-dataset").on("submit", function(e) {
      e.preventDefault();

      const formData = $(this).serializeArray();

      $.ajax({
        url: BASE_URL + "/datasets/update",
        method: "POST",
        data: $.param(formData),
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-edit-dataset").modal("hide");
            loadDatasets(currentPage, currentSearch, currentFilter);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error updating dataset:', error);
          showAlert('Terjadi kesalahan saat memperbarui dataset', 'error');
        }
      });
    });

    // Delete dataset
    window.deleteDataset = function(id) {
      showConfirm("Yakin ingin menghapus dataset ini?", function(ok) {
        if (!ok) return;

        $.ajax({
          url: BASE_URL + "/datasets/delete",
          method: "POST",
          data: {
            id: id
          },
          dataType: "json",
          success: function(res) {
            showAlert(res.message, res.success ? "success" : "error");
            if (res.success) loadDatasets(currentPage, currentSearch, currentFilter);
          },
          error: function(xhr, status, error) {
            console.error('Error deleting dataset:', error);
            showAlert('Terjadi kesalahan saat menghapus dataset', 'error');
          }
        });
      });
    };

    // Event handler untuk filter
    $('#categoryFilter').on('change', function() {
      const filter = $(this).val();
      loadDatasets(1, currentSearch, filter, DEFAULT_LIMIT);
    });

    // Search dengan debounce
    let _searchTimeout = null;
    $('#searchDataset').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        currentSearch = q;
        loadDatasets(1, q, currentFilter, DEFAULT_LIMIT);
      }, 300);
    });


    $(document).ready(function() {
      // initial load datasets
      loadDatasets(1, '', '', DEFAULT_LIMIT);

      // Reset form when modal is closed
      $('#modal-add-dataset').on('hidden.bs.modal', function() {
        $('#form-add-dataset')[0].reset();
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