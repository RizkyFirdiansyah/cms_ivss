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

    <!-- Publications -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Publikasi</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari publikasi..." id="searchPublication">
              </div>
              <select id="categoryFilter" class="form-control" style="max-width: 200px;">
                <option value="">Semua Kategori</option>
                <!-- Options akan diisi oleh JavaScript -->
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
            <label class="form-label">Judul Publikasi</label>
            <input name="title" type="text" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tahun Publikasi</label>
                <input name="publication_year" type="number" class="form-control" min="1900" max="2030" value="<?= date('Y') ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Link Publikasi</label>
                <input name="link" type="url" class="form-control" placeholder="https://example.com" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="categories[]" class="form-control categories-select" multiple style="width: 100%;">
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
            <label class="form-label">Judul Publikasi</label>
            <input name="title" id="edit-title" type="text" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tahun Publikasi</label>
                <input name="publication_year" id="edit-publication_year" type="number" class="form-control" min="1900" max="2030" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Link Publikasi</label>
                <input name="link" id="edit-link" type="url" class="form-control" placeholder="https://example.com" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="categories[]" id="edit-categories" class="form-control categories-select" multiple style="width: 100%;">
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
    let currentCategory = '';
    let globalCategories = [];

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

    // Show edit publication - MENGGUNAKAN STYLE SAMA SEPERTI NEWS
    window.showEditPublication = function(el) {
      const $el = $(el);
      const id = $el.data('id');
      const title = $el.data('title');
      const publication_year = $el.data('year');
      const link = $el.data('link');

      let selectedCategoryIds = [];
      const categoryIdsData = $el.data('categories');

      if (categoryIdsData) {
        if (typeof categoryIdsData === 'string') {
          selectedCategoryIds = JSON.parse(categoryIdsData);
        } else if (Array.isArray(categoryIdsData)) {
          selectedCategoryIds = categoryIdsData;
        }
        // Pastikan IDs adalah integer
        selectedCategoryIds = selectedCategoryIds.map(id => parseInt(id)).filter(id => !isNaN(id));
      }

      // Isi input biasa
      $('#edit-id').val(id);
      $('#edit-title').val(title);
      $('#edit-publication_year').val(publication_year);
      $('#edit-link').val(link);

      const $select = $('#edit-categories');

      $('#modal-edit-publication').off('shown.bs.modal').on('shown.bs.modal', function() {

        if ($select.hasClass('select2-hidden-accessible')) {
          $select.select2('destroy');
        }

        $select.empty();

        globalCategories.forEach(cat => {
          const option = new Option(cat.name, cat.id, false, false);
          $select.append(option);
        });

        $select.select2({
          dropdownParent: $('#modal-edit-publication'),
          placeholder: "Pilih kategori...",
          allowClear: true,
          tags: false
        });

        $select.val(selectedCategoryIds).trigger('change');
      });

      // Tampilkan modal
      new bootstrap.Modal('#modal-edit-publication').show();
    };

    // Load category list untuk dropdown dan filter
    function loadCategories() {
      $.ajax({
        url: BASE_URL + '/publikasi/categories',
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
              tags: false
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

    // Render publication row - PERBAIKAN: Kirim categories sebagai JSON string
    function renderPublicationRow(pub) {
      const categoriesHtml = pub.categories && pub.categories !== '' ?
        pub.categories.split(', ').map(cat =>
          `<span class="badge bg-gradient-info text-white me-1 mb-1 px-2 py-1">${cat}</span>`
        ).join('') :
        '<span class="text-muted">-</span>';

      const lastUpdated = pub.last_updated ? new Date(pub.last_updated).toLocaleDateString('id-ID') : '-';

      let categoryIds = [];
      if (pub.category_ids) {
        if (Array.isArray(pub.category_ids)) {
          categoryIds = pub.category_ids;
        } else if (typeof pub.category_ids === 'string') {
          // Jika dari database berupa string, parse ke array
          categoryIds = pub.category_ids.split(',').map(id => id.trim()).filter(id => id !== '');
        }
      }

      const categoryIdsJson = JSON.stringify(categoryIds);

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
          <td class="text-center">
            <button class="btn mb-0 px-3 btn-warning btn-sm text-xs" 
              onclick="showEditPublication(this)"
              data-id="${pub.id}"
              data-title="${pub.title ? pub.title.replace(/"/g, '&quot;') : ''}"
              data-link="${pub.link || ''}"
              data-year="${pub.publication_year}"
              data-categories='${categoryIdsJson}'>
              <i class="fa fa-edit"></i>
            </button>
            <button class="btn mb-0 px-3 btn-danger btn-sm text-xs" 
              onclick="deletePublication(${pub.id})">
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
    }

    // Load publications
    function loadPublications(page = 1, search = '', category = '', limit = DEFAULT_LIMIT) {
      const tbody = $('#publicationTableBody');
      const pagination = $('#pagination');
      tbody.html(`<tr><td colspan="6" class="text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>`);
      pagination.empty();

      const requestData = {
        page: page,
        limit: limit,
        search: search
      };

      if (category) {
        requestData.category_id = category;
      }

      $.ajax({
        url: BASE_URL + '/publikasi/list',
        method: 'GET',
        dataType: 'json',
        data: requestData,
        success: function(res) {
          tbody.empty();
          if (!res || !res.success || !res.data || res.data.length === 0) {
            let message = 'Tidak ada data publikasi.';
            if (search) message += ` untuk pencarian "${search}"`;
            if (category) {
              const categoryName = $('#categoryFilter option:selected').text();
              message += ` dalam kategori "${categoryName}"`;
            }
            tbody.append(`<tr><td colspan="6" class="text-center text-muted">${message}</td></tr>`);
            return;
          }

          res.data.forEach(p => tbody.append(renderPublicationRow(p)));

          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';

          // Previous button
          if (page > 1) {
            html += `<button class="btn btn-sm btn-outline-primary mx-1" onclick="loadPublications(${page - 1}, '${encodeURIComponent(search)}', '${category}', ${limit})">
                      <i class="fas fa-chevron-left"></i>
                    </button>`;
          }

          // Page numbers
          const startPage = Math.max(1, page - 2);
          const endPage = Math.min(totalPages, startPage + 4);

          for (let i = startPage; i <= endPage; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 ${cls} mx-1" onclick="loadPublications(${i}, '${encodeURIComponent(search)}', '${category}', ${limit})">${i}</button>`;
          }

          // Next button
          if (page < totalPages) {
            html += `<button class="btn btn-sm btn-outline-primary mx-1" onclick="loadPublications(${page + 1}, '${encodeURIComponent(search)}', '${category}', ${limit})">
                      <i class="fas fa-chevron-right"></i>
                    </button>`;
          }

          pagination.html(html);
          currentPage = page;
          currentSearch = search;
          currentCategory = category;
        },
        error: function(xhr, status, err) {
          console.error('Error loading publications:', status, err);
          tbody.html(`<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>`);
        }
      });
    }

    // Add publication
    $("#form-add-publication").on("submit", function(e) {
      e.preventDefault();

      const selectedCategories = $('#form-add-publication select').val() || [];

      const formData = $(this).serializeArray();
      formData.push({
        name: 'categories',
        value: JSON.stringify(selectedCategories)
      });

      $.ajax({
        url: BASE_URL + "/publikasi/create",
        method: "POST",
        data: $.param(formData),
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-add-publication").modal("hide");
            $("#form-add-publication")[0].reset();
            $('.categories-select').val(null).trigger('change');
            loadPublications(currentPage, currentSearch, currentCategory);
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

      const selectedCategories = $('#edit-categories').val() || [];
      const formData = $(this).serializeArray();
      formData.push({
        name: 'categories',
        value: JSON.stringify(selectedCategories)
      });

      $.ajax({
        url: BASE_URL + "/publikasi/update",
        method: "POST",
        data: $.param(formData),
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-edit-publication").modal("hide");
            loadPublications(currentPage, currentSearch, currentCategory);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error updating publication:', error);
          showAlert('Terjadi kesalahan saat memperbarui publikasi', 'error');
        }
      });
    });

    // Delete publication
    window.deletePublication = function(id) {
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
            if (res.success) loadPublications(currentPage, currentSearch, currentCategory);
          },
          error: function(xhr, status, error) {
            console.error('Error deleting publication:', error);
            showAlert('Terjadi kesalahan saat menghapus publikasi', 'error');
          }
        });
      });
    };

    // Search dengan debounce
    let _searchTimeout = null;
    $('#searchPublication').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        currentSearch = q;
        loadPublications(1, q, currentCategory, DEFAULT_LIMIT);
      }, 300);
    });

    // Category filter change
    $('#categoryFilter').on('change', function() {
      const categoryId = $(this).val();
      currentCategory = categoryId;
      loadPublications(1, currentSearch, categoryId, DEFAULT_LIMIT);
    });

    // Init 
    $(document).ready(function() {
      // Load categories untuk dropdown dan filter
      loadCategories();

      // initial load publikasi
      loadPublications(1, '', '', DEFAULT_LIMIT);

      // Reset form when modal is closed
      $('#modal-add-publication').on('hidden.bs.modal', function() {
        $('#form-add-publication')[0].reset();
        $('.categories-select').val(null).trigger('change');
      });

      // Reset edit modal ketika ditutup
      $('#modal-edit-publication').on('hidden.bs.modal', function() {
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