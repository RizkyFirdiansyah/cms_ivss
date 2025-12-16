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
</head>

<body class="g-sidenav-show  bg-gray-100">

  <?php include('includes/sidebar.php') ?>

  <main class="main-content position-relative border-radius-lg ">

    <?php include('includes/navbar.php') ?>

    <!-- Kategori -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Kategori</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari kategori..." id="searchCategory">
              </div>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-category" data-bs-toggle="modal" data-bs-target="#modal-add-category">
                <i class="fa fa-plus"></i> Tambah Kategori
              </button>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Kategori</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody id="categories-container">
                  <!-- Data diisi Javascript -->
                </tbody>
              </table>
            </div>

            <div id="pagination" class="mt-4 text-center"></div>

          </div>
        </div>
      </div>
    </div>
    <!-- End kategori -->
  </main>

  <!-- Modal: Tambah Kategori -->
  <div class="modal fade" id="modal-add-category" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-add-category" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Kategori</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="add-category-alert"></div>
          <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input name="name" type="text" class="form-control" placeholder="Masukkan nama kategori" required
              minlength="2" maxlength="100">
            <div class="form-text text-xs">Minimal 2 karakter, maksimal 100 karakter</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  <!-- End Modal: Tambah Kategori -->

  <!-- Modal Edit Kategori -->
  <div class="modal fade" id="modal-edit-category" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-edit-category" class="modal-content">
        <input type="hidden" name="id" id="edit-id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Kategori</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="edit-category-alert"></div>
          <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input name="name" id="edit-name" class="form-control" placeholder="Masukkan nama kategori" required
              minlength="2" maxlength="100">
            <div class="form-text text-xs">Minimal 2 karakter, maksimal 100 karakter</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn bg-gradient-primary" type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  <!-- End Modal Edit Kategori -->

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

  <?php require_once 'includes/footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";
    const DEFAULT_LIMIT = 10;
    let currentPage = 1;

    // Javascript Function Helpers
    // Show Alert
    function showAlert(message, type = 'info') {
      const icons = {
        success: 'fa-check-circle text-success',
        error: 'fa-times-circle text-danger',
        warning: 'fa-exclamation-triangle text-warning',
        info: 'fa-info-circle text-primary'
      };
      $('#alert-icon').attr('class', `fa ${icons[type] || icons.info} mb-3`).css('font-size', '1.6rem');
      $('#alert-message').text(message);
      const modal = new bootstrap.Modal($('#modal-alert')[0]);
      modal.show();
      setTimeout(() => {
        modal.hide();
      }, 1000);
    }

    // Show Confirm
    function showConfirm(message, callback) {
      $('#confirm-message').text(message);
      const modalEl = $('#modal-confirm')[0];
      const modal = new bootstrap.Modal(modalEl);

      $('#confirm-yes').off('click').on('click', function() {
        modal.hide();
        if (typeof callback === 'function') callback(true);
      });

      $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', function() {
        if (typeof callback === 'function') callback(false);
      });

      modal.show();
    }

    // Show edit kategori
    window.showEditCategory = function(id, name) {
      $('#edit-id').val(id);
      $('#edit-name').val(name);
      const modal = new bootstrap.Modal($('#modal-edit-category')[0]);
      modal.show();
    };

    // Render kategori row 
    function renderCategoryRow(cat) {
      return `
        <tr>
          <td>
            <div class="d-flex px-2 py-1">
              <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">${cat.name}</h6>
              </div>
            </div>
          </td>
          <td class="text-center text-lg"><i class="fa-solid fa-circle-check text-success"></i></td>
          <td class="text-center">
            <button class="btn mb-0 px-3 btn-warning btn-sm text-xs" 
              onclick="showEditCategory(${cat.id}, '${cat.name}')"
              title="Edit kategori">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn mb-0 px-3 btn-danger btn-sm text-xs" 
              onclick="deleteCategory(${cat.id}, '${cat.name}')"
              title="Hapus kategori">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
    }

    // Load kategori 
    function loadCategories(page = 1, search = '', limit = DEFAULT_LIMIT) {
      const tbody = $('#categories-container');
      const pagination = $('#pagination');

      tbody.html(`
        <tr>
          <td colspan="3" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td>
        </tr>
      `);
      pagination.empty();

      $.ajax({
        url: BASE_URL + '/kategori/list',
        method: 'GET',
        dataType: 'json',
        data: {
          page: page,
          limit: limit,
          search: search
        },
        success: function(res) {
          tbody.empty();
          if (!res.success || !res.data || res.data.length === 0) {
            tbody.append(`
              <tr>
                <td colspan="3" class="text-center text-muted py-4">
                  <i class="fas fa-inbox me-2"></i>Tidak ada data kategori.
                </td>
              </tr>
            `);
            return;
          }

          // Render data
          res.data.forEach(cat => tbody.append(renderCategoryRow(cat)));

          // Pagination
          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';
          for (let i = 1; i <= totalPages; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 py-2 ${cls} mx-1" onclick="loadCategories(${i}, '${encodeURIComponent(search)}', ${limit})">${i}</button>`;
          }
          pagination.html(html);
          currentPage = page;
        },
        error: function(xhr, status, err) {
          console.error('Error loading categories:', xhr.responseText);
          tbody.html(`
            <tr>
              <td colspan="3" class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat data.
              </td>
            </tr>
          `);
        }
      });
    }

    // Add kategori
    $("#form-add-category").on("submit", function(e) {
      e.preventDefault();
      const formData = $(this).serialize();

      $.ajax({
        url: BASE_URL + "/kategori/create",
        method: "POST",
        data: formData,
        dataType: "json",
        success: function(res) {
          if (res.success) {
            showAlert(res.message, "success");
            $("#modal-add-category").modal("hide");
            $("#form-add-category")[0].reset();
            loadCategories(currentPage);
          } else {
            showAlert(res.message || 'Gagal menambah kategori.', 'error');
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menambah kategori.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'warning');
        }
      });
    });

    // Update kategori
    $("#form-edit-category").on("submit", function(e) {
      e.preventDefault();
      const formData = $(this).serialize();

      const name = $(this).find('input[name="name"]').val().trim();
      if (name.length < 2) {
        showAlert('Nama kategori minimal 2 karakter', 'error');
        return;
      }

      $.ajax({
        url: BASE_URL + "/kategori/update",
        method: "POST",
        data: formData,
        dataType: "json",
        success: function(res) {
          if (res.success) {
            showAlert(res.message, "success");
            $("#modal-edit-category").modal("hide");
            loadCategories(currentPage);
          } else {
            showAlert(res.message || 'Gagal mengupdate kategori.', 'error');
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menambah kategori.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'warning');
        }
      });
    });

    // Delete kategori
    function deleteCategory(id, name = '') {
      const categoryName = name || `ID ${id}`;

      showConfirm(`Yakin ingin menghapus kategori "${categoryName}"?`, function(confirmed) {
        if (!confirmed) return;

        $.ajax({
          url: BASE_URL + "/kategori/delete",
          method: "POST",
          data: {
            id: id
          },
          dataType: "json",
          success: function(res) {
            if (res.success) {
              showAlert(res.message, res.success);
              loadCategories(currentPage);
            } else {
              showAlert(res.message || 'Gagal menghapus kategori.', 'error');
            }
          },
          error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat menghapus kategori.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            }
            showAlert(errorMessage, 'warning');
          }
        });
      });
    }

    // Search
    let _searchTimeout = null;
    $('#searchCategory').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        loadCategories(1, q, DEFAULT_LIMIT);
      }, 300);
    });

    // Init
    $(document).ready(function() {
      // Initial Load
      loadCategories(1, '', DEFAULT_LIMIT);

      $('#modal-add-category').on('hidden.bs.modal', function() {
        $('#form-add-category')[0].reset();
        $('#add-category-alert').empty();
      });

      $('#modal-edit-category').on('hidden.bs.modal', function() {
        $('#edit-category-alert').empty();
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