<?php
$page_title = "Publikasi";
$page_breadcrumb = ["Pages", "Publikasi"];
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
</head>

<body class="g-sidenav-show  bg-gray-100">

  <?php include('includes/sidebar.php') ?>

  <main class="main-content position-relative border-radius-lg ">

    <?php include('includes/navbar.php') ?>

    <!-- Publications -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Manajemen Publikasi</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari publikasi..." id="searchPublication">
              </div>
              <select class="form-select w-25" id="categoryFilter">
                <option value="">Semua Kategori</option>
                <!-- Categories will be populated by JavaScript -->
              </select>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-publication" data-bs-toggle="modal" data-bs-target="#modal-add-publication">
                <i class="fa fa-plus"></i> Tambah Publikasi
              </button>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul Publikasi</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tahun</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kategori</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Link</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Terakhir Diupdate</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                  </tr>
                </thead>
                <tbody id="publicationTableBody">
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
    <!-- End Publications -->
  </main>

  <!-- Modal: Tambah Publikasi -->
  <div class="modal fade" id="modal-add-publication" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form id="form-add-publication" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Publikasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="add-publication-alert"></div>
          <div class="mb-3">
            <label class="form-label">Judul Publikasi <span class="text-danger">*</span></label>
            <input name="title" type="text" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tahun Publikasi <span class="text-danger">*</span></label>
                <input name="publication_year" type="number" class="form-control" min="1900" max="2030" value="<?= date('Y') ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Link Publikasi <span class="text-danger">*</span></label>
                <input name="link" type="url" class="form-control" placeholder="https://example.com" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="categories[]" class="form-select" multiple id="categories-select-add" style="height: 120px;">
              <!-- Categories will be populated by JavaScript -->
            </select>
            <small class="text-muted">Tahan Ctrl/Cmd untuk memilih multiple kategori</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal: Edit Publikasi -->
  <div class="modal fade" id="modal-edit-publication" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form id="form-edit-publication" class="modal-content">
        <input type="hidden" name="id" id="edit-id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Publikasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="edit-publication-alert"></div>
          <div class="mb-3">
            <label class="form-label">Judul Publikasi <span class="text-danger">*</span></label>
            <input name="title" id="edit-title" type="text" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tahun Publikasi <span class="text-danger">*</span></label>
                <input name="publication_year" id="edit-publication_year" type="number" class="form-control" min="1900" max="2030" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Link Publikasi <span class="text-danger">*</span></label>
                <input name="link" id="edit-link" type="url" class="form-control" placeholder="https://example.com" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="categories[]" class="form-select" multiple id="categories-select-edit" style="height: 120px;">
              <!-- Categories will be populated by JavaScript -->
            </select>
            <small class="text-muted">Tahan Ctrl/Cmd untuk memilih multiple kategori</small>
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

  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";
    const DEFAULT_LIMIT = 5;
    let currentPage = 1;
    let categories = [];

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

    // Load categories
    function loadCategories() {
      $.ajax({
        url: BASE_URL + '/publikasi/categories',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            categories = res.data;

            // Populate category filter
            const categoryFilter = $('#categoryFilter');
            categoryFilter.empty();
            categoryFilter.append('<option value="">Semua Kategori</option>');
            categories.forEach(cat => {
              categoryFilter.append(`<option value="${cat.id}">${cat.name}</option>`);
            });

            // Populate categories select in add modal
            const categoriesSelectAdd = $('#categories-select-add');
            categoriesSelectAdd.empty();
            categories.forEach(cat => {
              categoriesSelectAdd.append(`<option value="${cat.id}">${cat.name}</option>`);
            });

            // Populate categories select in edit modal
            const categoriesSelectEdit = $('#categories-select-edit');
            categoriesSelectEdit.empty();
            categories.forEach(cat => {
              categoriesSelectEdit.append(`<option value="${cat.id}">${cat.name}</option>`);
            });
          }
        },
        error: function(xhr) {
          console.error('Error loading categories:', xhr);
          showAlert('Gagal memuat data kategori', 'error');
        }
      });
    }

    // Render publication row
    function renderPublicationRow(pub) {
      console.log('Rendering publication:', pub);

      const categoriesHtml = pub.categories && pub.categories !== '' ?
        pub.categories.split(', ').map(cat =>
          `<span class="badge bg-gradient-info text-white me-1 mb-1 px-2 py-1">${cat}</span>`
        ).join('') :
        '<span class="text-muted">-</span>';

      const lastUpdated = pub.last_updated ? new Date(pub.last_updated).toLocaleDateString('id-ID') : '-';

      // Pastikan category_ids adalah array
      const categoryIds = Array.isArray(pub.category_ids) ? pub.category_ids : [];
      const categoryIdsString = categoryIds.join(',');

      console.log('Publication category data:', {
        id: pub.id,
        categories: pub.categories,
        categoryIds: categoryIds,
        categoryIdsString: categoryIdsString
      });

      return `
        <tr>
          <td>
            <div class="d-flex flex-column justify-content-center">
              <h6 class="mb-0 text-sm">${pub.title}</h6>
            </div>
          </td>
          <td class="text-center text-sm align-middle">${pub.publication_year}</td>
          <td class="text-center align-middle">
            <div class="d-flex flex-wrap justify-content-center">
              ${categoriesHtml}
            </div>
          </td>
          <td class="text-center align-middle">
            <a href="${pub.link}" target="_blank" class="btn btn-sm bg-warning text-white px-3">
              <i class="fa fa-external-link me-1"></i>Link
            </a>
          </td>
          <td class="text-center text-sm align-middle">${lastUpdated}</td>
          <td class="text-center align-middle">
            <button class="btn btn-xs btn-secondary me-1" 
              onclick="showEditPublication(this)"
              data-id="${pub.id}"
              data-title="${pub.title ? pub.title.replace(/"/g, '&quot;') : ''}"
              data-link="${pub.link || ''}"
              data-year="${pub.publication_year}"
              data-categories="${categoryIdsString}">
              <i class="fa fa-edit me-1"></i>
            </button>
            <button class="btn btn-xs btn-danger" 
              onclick="deletePublication(${pub.id})">
              <i class="fa fa-trash me-1"></i>
            </button>
          </td>
        </tr>
      `;
    }

    // Load publications
    function loadPublications(page = 1, search = '', category_id = '', limit = DEFAULT_LIMIT) {
      const tbody = $('#publicationTableBody');
      const pagination = $('#pagination');
      tbody.html(`<tr><td colspan="6" class="text-center text-muted">Memuat data...</td></tr>`);
      pagination.empty();

      $.ajax({
        url: BASE_URL + '/publikasi/list',
        method: 'GET',
        dataType: 'json',
        data: {
          page: page,
          limit: limit,
          search: search,
          category_id: category_id
        },
        success: function(res) {
          tbody.empty();
          if (!res || !res.success || !res.data || res.data.length === 0) {
            tbody.append(`<tr><td colspan="6" class="text-center text-muted">Tidak ada data publikasi.</td></tr>`);
            return;
          }

          res.data.forEach(p => tbody.append(renderPublicationRow(p)));

          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';
          for (let i = 1; i <= totalPages; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 py-2 ${cls} mx-1" onclick="loadPublications(${i}, '${encodeURIComponent(search)}', '${category_id}', ${limit})">${i}</button>`;
          }
          pagination.html(html);
          currentPage = page;
        },
        error: function(xhr, status, err) {
          console.error('Error loading publications:', status, err);
          tbody.html(`<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>`);
        }
      });
    }

    // Show edit publication - DENGAN DEBUGGING
    window.showEditPublication = function(el) {
      console.log('=== DEBUG EDIT BUTTON ===');
      const $el = $(el);

      // Debug: lihat semua data attributes
      console.log('Button element:', $el);
      console.log('All data attributes:', $el.data());

      const id = $el.data('id');
      const title = $el.data('title');
      const publication_year = $el.data('year');
      const link = $el.data('link');
      const categories = $el.data('categories');

      console.log('Raw data:', {
        id,
        title,
        publication_year,
        link,
        categories
      });

      // Pastikan nilai tidak undefined
      if (!id) {
        console.error('❌ ERROR: ID tidak ditemukan!');
        showAlert('Error: ID publikasi tidak valid', 'error');
        return;
      }

      // Set values ke form edit
      $('#edit-id').val(id || '');
      $('#edit-title').val(title || '');
      $('#edit-publication_year').val(publication_year || '');
      $('#edit-link').val(link || '');

      // Debug categories
      console.log('Categories raw:', categories);
      console.log('Categories type:', typeof categories);

      // Process categories
      let categoryIds = [];
      if (categories) {
        if (typeof categories === 'string') {
          categoryIds = categories.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
        } else if (Array.isArray(categories)) {
          categoryIds = categories.map(id => parseInt(id)).filter(id => !isNaN(id));
        }
      }

      console.log('Processed category IDs:', categoryIds);

      // Set selected categories
      if (categoryIds.length > 0) {
        $('#categories-select-edit').val(categoryIds);
        console.log('✅ Categories set successfully:', categoryIds);
      } else {
        $('#categories-select-edit').val([]);
        console.log('ℹ️ No categories selected');
      }

      // Show modal
      const modalElement = document.getElementById('modal-edit-publication');
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('✅ Modal shown successfully');
      } else {
        console.error('❌ ERROR: Modal element tidak ditemukan!');
      }

      console.log('=== END DEBUG ===');
    };

    // Render publication row - DENGAN DEBUGGING
    function renderPublicationRow(pub) {
      console.log('Rendering publication:', pub);

      const categoriesHtml = pub.categories && pub.categories !== '' ?
        pub.categories.split(', ').map(cat =>
          `<span class="badge bg-gradient-info me-1 mb-1">${cat}</span>`
        ).join('') :
        '<span class="text-muted">-</span>';

      const lastUpdated = pub.last_updated ? new Date(pub.last_updated).toLocaleDateString('id-ID') : '-';

      // Pastikan category_ids adalah array
      const categoryIds = Array.isArray(pub.category_ids) ? pub.category_ids : [];
      const categoryIdsString = categoryIds.join(',');

      console.log('Publication category data:', {
        id: pub.id,
        categories: pub.categories,
        categoryIds: categoryIds,
        categoryIdsString: categoryIdsString
      });

      return `
        <tr>
          <td>
            <div class="d-flex flex-column justify-content-center">
              <h6 class="mb-0 text-sm">${pub.title}</h6>
            </div>
          </td>
          <td class="text-center text-sm align-middle">${pub.publication_year}</td>
          <td class="text-center align-middle">
            <div class="d-flex flex-wrap justify-content-center">
              ${categoriesHtml}
            </div>
          </td>
          <td class="text-center align-middle">
            <a href="${pub.link}" target="_blank" class="btn btn-sm bg-gradient-warning text-white px-3">
              <i class="fa fa-external-link me-1"></i>Link
            </a>
          </td>
          <td class="text-center text-sm align-middle">${lastUpdated}</td>
          <td class="text-center align-middle">
            <button class="btn btn-xs btn-secondary me-1" 
              onclick="showEditPublication(this)"
              data-id="${pub.id}"
              data-title="${pub.title ? pub.title.replace(/"/g, '&quot;') : ''}"
              data-link="${pub.link || ''}"
              data-year="${pub.publication_year}"
              data-categories="${categoryIdsString}">
              <i class="fa fa-edit me-1"></i>
            </button>
            <button class="btn btn-xs btn-danger" 
              onclick="deletePublication(${pub.id})">
              <i class="fa fa-trash me-1"></i>
            </button>
          </td>
        </tr>
          `;
    }
    // Delete publication
    window.deletePublication = function(id) {
      console.log('Deleting publication ID:', id);
      showConfirm("Yakin ingin menghapus publikasi ini?", function(ok) {
        if (!ok) return;

        $.ajax({
          url: BASE_URL + "/publikasi/delete",
          method: "POST",
          data: {
            id: id
          },
          dataType: "json",
          success: function(res) {
            showAlert(res.message, res.success ? "success" : "error");
            if (res.success) loadPublications(currentPage);
          },
          error: function(xhr, status, error) {
            console.error('Error deleting publication:', error);
            showAlert('Terjadi kesalahan saat menghapus publikasi', 'error');
          }
        });
      });
    };

    // Add publication
    $("#form-add-publication").on("submit", function(e) {
      e.preventDefault();
      const formData = $(this).serialize();

      $.ajax({
        url: BASE_URL + "/publikasi/create",
        method: "POST",
        data: formData,
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-add-publication").modal("hide");
            $("#form-add-publication")[0].reset();
            $('#categories-select-add').val([]);
            loadPublications(currentPage);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error adding publication:', error);
          showAlert('Terjadi kesalahan saat menambah publikasi', 'error');
        }
      });
    });

    // Update publication
    $("#form-edit-publication").on("submit", function(e) {
      e.preventDefault();
      const formData = $(this).serialize();

      $.ajax({
        url: BASE_URL + "/publikasi/update",
        method: "POST",
        data: formData,
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-edit-publication").modal("hide");
            loadPublications(currentPage);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error updating publication:', error);
          showAlert('Terjadi kesalahan saat memperbarui publikasi', 'error');
        }
      });
    });

    // Search
    let _searchTimeout = null;
    $('#searchPublication').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        loadPublications(1, q, $('#categoryFilter').val(), DEFAULT_LIMIT);
      }, 300);
    });

    // Category filter change
    $('#categoryFilter').change(function() {
      const categoryId = $(this).val();
      loadPublications(1, $('#searchPublication').val(), categoryId, DEFAULT_LIMIT);
    });

    // Reset add modal when closed
    $('#modal-add-publication').on('hidden.bs.modal', function() {
      $('#form-add-publication')[0].reset();
      $('#categories-select-add').val([]);
    });

    // Init dengan testing
    $(document).ready(function() {
      console.log('🚀 Publications page loaded');

      loadCategories();
      loadPublications(1, '', '', DEFAULT_LIMIT);

      // Test function untuk debug
      window.testMultipleCategories = function() {
        console.log('🧪 Testing multiple categories...');

        // Test data dengan multiple categories
        const testData = {
          id: 999,
          title: 'Test Publication Multiple Categories',
          publication_year: 2024,
          link: 'https://example.com',
          categories: 'Research, Technology, Education', // Multiple categories
          category_ids: [1, 2, 3] // Multiple category IDs
        };

        // Test render function
        const testRow = renderPublicationRow(testData);
        console.log('Test render result:', testRow);

        // Test edit function
        const testBtn = $('<button>')
          .attr('data-id', testData.id)
          .attr('data-title', testData.title)
          .attr('data-year', testData.publication_year)
          .attr('data-link', testData.link)
          .attr('data-categories', testData.category_ids.join(','));

        showEditPublication(testBtn[0]);
      };
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