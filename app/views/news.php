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
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">

  <?php include('includes/sidebar.php') ?> <!-- Sidebar -->

  <main class="main-content position-relative border-radius-lg ">

    <?php include('includes/navbar.php') ?><!-- Navbar -->

    <!-- Berita -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Berita</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3"> <!-- Search n ADD Button -->
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari berita..." id="searchNews">
              </div>
              <select id="categoryFilter" class="form-control" style="max-width: 200px;">
                <option value="">Semua Kategori</option>
                <!-- Options akan diisi oleh JavaScript -->
              </select>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-news" data-bs-toggle="modal" data-bs-target="#modal-add-news">
                <i class="fa fa-plus"></i> Tambah Berita
              </button>
            </div>
            <div class="row g-3 my-3" id="news-container"></div>
            <div id="pagination" class="mt-4 text-center"></div>

          </div>
        </div>
        <!-- End berita -->
  </main>

  <!-- Modal: Tambah Berita -->
  <div class="modal fade" id="modal-add-news" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-add-news" class="modal-content" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Berita</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="add-news-alert"></div>
          <div class="mb-3">
            <label class="form-label">Judul Berita</label>
            <input name="title" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Konten</label>
            <textarea name="content" class="form-control" cols="30" rows="5" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="categories[]" class="form-control categories-select" multiple style="width: 100%;">
              <!-- Data diisi JavaScript -->
            </select>
          </div>
          <div class="mb-3">
            <label class="form-control-label">Foto</label>
            <input class="form-control" type="file" name="photo">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  <!-- End Modal: Tambah Berita -->

  <!-- Modal Edit Berita -->
  <div class="modal fade" id="modal-edit-news" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-edit-news" class="modal-content" enctype="multipart/form-data">

        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="old_photo" id="edit-old-photo">

        <div class="modal-header">
          <h5 class="modal-title">Edit Berita</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div id="edit-news-alert"></div>
          <div class="mb-3">
            <label class="form-label">Judul Berita</label>
            <input name="title" id="edit-title" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Konten</label>
            <textarea name="content" id="edit-content" class="form-control" cols="30" rows="5" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="categories[]" id="edit-categories" class="form-control categories-select" multiple style="width: 100%;">
              <!-- Options akan diisi oleh JavaScript -->
            </select>
          </div>
          <div class="text-center">
            <img id="edit-preview" class="img-fluid rounded mb-2" style="max-height: 200px;">
          </div>
          <div class="mb-3">
            <label class="form-label">Foto Baru (opsional)</label>
            <input type="file" name="photo" class="form-control">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn bg-gradient-primary" type="submit">Simpan</button>
        </div>

      </form>
    </div>
  </div>
  <!-- End Modal Edit Berita -->

  <!-- Modal Detail Berita -->
  <div class="modal fade" id="modal-detail-news" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Berita</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-3">
            <img id="detail-preview" class="img-fluid rounded" style="max-height: 300px;">
          </div>
          <h4 id="detail-title" class="mb-3"></h4>
          <div class="mb-3">
            <strong>Kategori:</strong>
            <span id="detail-categories" class="ms-2"></span>
          </div>
          <div class="mb-3">
            <strong>Dibuat oleh:</strong>
            <span id="detail-author" class="ms-2"></span>
          </div>
          <div class="mb-3">
            <strong>Tanggal:</strong>
            <span id="detail-date" class="ms-2"></span>
          </div>
          <div class="content-box">
            <p id="detail-content" class="text-justify"></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal Detail Berita -->

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

  <?php require_once 'includes/footer.php'; ?> <!-- Footer -->

  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> <!-- Link Jquery -->
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";
    const DEFAULT_LIMIT = 6;
    let currentPage = 1;
    let currentSearch = '';
    let currentCategory = '';
    let globalCategories = [];

    // Javascript Function Helpers
    // Show alert
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

    // Show confirm
    function showConfirm(message, callback) {
      $('#confirm-message').html(message);
      const modalEl = $('#modal-confirm')[0];
      const modal = new bootstrap.Modal(modalEl);
      $('#confirm-yes').off('click').on('click', function() {
        modal.hide();
        if (typeof callback === 'function') callback(true);
      });
      $('#confirm-no').off('click').on('click', function() {
        modal.hide();
        if (typeof callback === 'function') callback(false);
      });
      modal.show();
    }

    // Show edit berita 
    window.showEditNews = function(el) {
      const $el = $(el);
      const id = $el.data('id');
      const title = $el.data('title');
      const content = $el.data('content');
      const photo = $el.data('photo');

      let selectedCategoryIds = [];
      const categoryIdsData = $el.data('category-ids');

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
      $('#edit-content').val(content);
      $('#edit-old-photo').val(photo);
      $('#edit-preview').attr('src', BASE_URL + '/uploads/news/' + (photo || 'default.jpg'));

      const $select = $('#edit-categories');

      $('#modal-edit-news').off('shown.bs.modal').on('shown.bs.modal', function() {

        if ($select.hasClass('select2-hidden-accessible')) {
          $select.select2('destroy');
        }

        $select.empty();

        globalCategories.forEach(cat => {
          const option = new Option(cat.name, cat.id, false, false);
          $select.append(option);
        });

        $select.select2({
          dropdownParent: $('#modal-edit-news'),
          placeholder: "Pilih kategori...",
          allowClear: true,
          tags: false
        });

        $select.val(selectedCategoryIds).trigger('change');
      });

      // Tampilkan modal
      new bootstrap.Modal('#modal-edit-news').show();
    };

    function truncateText(text, maxLength = 80) {
      if (!text) return '';
      if (text.length <= maxLength) {
        return text;
      }
      // Potong teks dan tambahkan elipsis
      return text.substring(0, maxLength) + '...';
    }

    // Format date
    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    }

    // Load category list untuk dropdown dan filter
    function loadCategories() {
      $.ajax({
        url: BASE_URL + '/kategori/getAll',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          globalCategories = res.data || [];

          // Initialize Select2 untuk form
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
        },
        error: function(xhr) {
          showAlert('Gagal memuat daftar kategori: ' + (xhr.responseText || 'Unknown Error'), 'warning');
        }
      });
    }

    // Render berita card
    function renderNewsCard(n) {
      const shortContent = truncateText(n.content, 100);
      // Categories display
      const categoriesText = n.categories ? n.categories.split(', ').map(cat =>
        `<span class="text-white px-2 py-1 rounded text-xxs bg-gradient-info me-1">${cat}</span>`
      ).join('') : '';

      const categoryIds = Array.isArray(n.category_ids) ? n.category_ids :
        (typeof n.category_ids === 'string' ? n.category_ids.split(',').map(id => id.trim()) : []);

      const categoriesArray = Array.isArray(n.categories) ? n.categories :
        (typeof n.categories === 'string' ? n.categories.split(', ') : []);

      return `
          <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card border-0 shadow-xs overflow-hidden h-100 rounded-4">
              <img src="${BASE_URL}/uploads/news/${n.photo || 'default.png'}" class="card-img-top object-fit-cover" style="height: 200px;" alt="${n.title}">
              <div class="card-body p-3">
                <h6 class="mb-1 fw-bold text-sm">${n.title}</h6>
                <div class="mb-2">${categoriesText}</div>
                <p class="text-muted text-xxs mb-2">${shortContent}</p>
                <div class="d-flex justify-content-between align-items-center text-xs text-muted mb-2">
                  <span><i class="fas fa-user me-1"></i>${n.author_name || 'Admin'}</span>
                  <span><i class="fas fa-calendar me-1"></i>${formatDate(n.created_at)}</span>
                </div>
                <div class="d-flex gap-2">
                  <button class="btn mb-0 px-3 btn-info btn-sm text-xs detail-btn"
                    onclick="showDetailNews(${n.id})">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn mb-0 px-3 btn-warning btn-sm text-xs edit-btn"
                    onclick="showEditNews(this)"
                    data-id="${n.id}"
                    data-title="${n.title}"
                    data-content="${n.content}"
                    data-photo="${n.photo}"
                    data-categories='${JSON.stringify(categoriesArray)}'
                    data-category-ids='${JSON.stringify(categoryIds)}'>
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn mb-0 px-3 btn-danger btn-sm text-xs"
                    onclick="deleteNews(${n.id})">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        `;
    }

    // Load berita dengan filter
    function loadNews(page = 1, search = '', category = '', limit = DEFAULT_LIMIT) {
      const container = $('#news-container');
      const pagination = $('#pagination');
      container.html(`<div class="col-12 text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</div>`);
      pagination.empty();

      const requestData = {
        page: page,
        limit: limit,
        search: search
      };

      // Tambahkan category filter jika dipilih
      if (category) {
        requestData.category_id = category;
      }

      $.ajax({
        url: BASE_URL + '/berita/list',
        method: 'GET',
        dataType: 'json',
        data: requestData,
        success: function(res) {
          container.empty();
          console.log(res);

          if (!res || !res.data || res.data.length === 0) {
            let message = 'Tidak ada data berita.';
            if (search) message += ` untuk pencarian "${search}"`;
            if (category) {
              const categoryName = $('#categoryFilter option:selected').text();
              message += ` dalam kategori "${categoryName}"`;
            }

            container.append(`<div class="col-12 text-center text-muted py-5"><i class="fas fa-inbox me-2"></i>${message}</div>`);
            return;
          }

          // Render cards
          res.data.forEach(n => container.append(renderNewsCard(n)));

          // Pagination
          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';

          // Previous button
          if (page > 1) {
            html += `<button class="btn btn-sm btn-outline-primary mx-1" onclick="loadNews(${page - 1}, '${encodeURIComponent(search)}', '${category}', ${limit})">
                      <i class="fas fa-chevron-left"></i>
                    </button>`;
          }

          // Page numbers
          const startPage = Math.max(1, page - 2);
          const endPage = Math.min(totalPages, startPage + 4);

          for (let i = startPage; i <= endPage; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 ${cls} mx-1" onclick="loadNews(${i}, '${encodeURIComponent(search)}', '${category}', ${limit})">${i}</button>`;
          }

          // Next button
          if (page < totalPages) {
            html += `<button class="btn btn-sm btn-outline-primary mx-1" onclick="loadNews(${page + 1}, '${encodeURIComponent(search)}', '${category}', ${limit})">
                      <i class="fas fa-chevron-right"></i>
                    </button>`;
          }

          pagination.html(html);
          currentPage = page;
          currentSearch = search;
          currentCategory = category;
        },
        error: function(xhr, status, err) {
          console.error('Error loading berita:', status, err);
          container.html(`<div class="col-12 text-center text-danger py-5"><i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat data.</div>`);
        }
      });
    }

    // Reset semua filter
    function resetFilters() {
      $('#searchNews').val('');
      $('#categoryFilter').val('');
      currentSearch = '';
      currentCategory = '';
      loadNews(1, '', '', DEFAULT_LIMIT);
    }

    // Add berita
    $("#form-add-news").on("submit", function(e) {
      e.preventDefault();
      const fd = new FormData(this);

      const selectedCategories = $('#form-add-news select').val();
      fd.append('categories', JSON.stringify(selectedCategories || []));

      $.ajax({
        url: BASE_URL + "/berita/create",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          if (res.success) {
            showAlert(res.message, "success");
            $("#modal-add-news").modal("hide");
            $("#form-add-news")[0].reset();
            $('.categories-select').val(null).trigger('change');
            loadNews(currentPage, currentSearch, currentCategory);
          } else {
            showAlert(res.message || 'Gagal menambah berita.', 'error');
          }
        },
        error: function(xhr, status, error) {
          let errorMessage = 'Terjadi kesalahan saat menambah berita.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'warning');
        }
      });
    });

    // Update berita
    $("#form-edit-news").on("submit", function(e) {
      e.preventDefault();

      const selectedCategories = $('#edit-categories').val();

      const fd = new FormData(this);
      fd.append('categories', JSON.stringify(selectedCategories || []));

      $.ajax({
        url: BASE_URL + "/berita/update",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          if (res.success) {
            showAlert(res.message, "success");
            $("#modal-edit-news").modal("hide");
            loadNews(currentPage, currentSearch, currentCategory);
          } else {
            showAlert(res.message || 'Gagal mengupdate berita.', 'error');
          }
        },
        error: function(xhr, status, error) {
          let errorMessage = 'Terjadi kesalahan saat update berita.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'warning');
        }
      });
    });

    // Show detail berita
    window.showDetailNews = function(id) {
      $.ajax({
        url: BASE_URL + "/berita/getDetail",
        method: "GET",
        data: {
          id: id
        },
        dataType: "json",
        success: function(res) {
          if (res.success) {
            const news = res.data;
            $('#detail-title').text(news.title);
            $('#detail-content').text(news.content);
            $('#detail-author').text(news.author_name || 'Admin');
            $('#detail-date').text(formatDate(news.created_at));
            $('#detail-preview').attr('src', BASE_URL + '/uploads/news/' + (news.photo || 'default.jpg'));

            // Format categories
            const categoriesText = news.categories ? news.categories.split(', ').map(cat =>
              `<span class="text-white px-2 py-1 rounded text-xxs bg-gradient-info me-1">${cat}</span>`
            ).join('') : '<span class="text-white px-2 py-1 rounded text-xxs bg-gradient-info me-1">Tidak ada kategori</span>';
            $('#detail-categories').html(categoriesText);

            const modal = new bootstrap.Modal($('#modal-detail-news')[0]);
            modal.show();
          } else {
            showAlert(res.message, "error");
          }
        }
      });
    };

    // Delete berita
    function deleteNews(id) {
      showConfirm("Yakin ingin menghapus berita ini?", function(confirmed) {
        if (!confirmed) return;

        $.ajax({
          url: BASE_URL + "/berita/delete",
          method: "POST",
          data: {
            id: id
          },
          dataType: "json",
          success: function(res) {
            if (res.success) {
              showAlert(res.message, "success");
              loadNews(currentPage, currentSearch, currentCategory);
            } else {
              showAlert(res.message || 'Gagal menghapus berita.', 'error');
            }
          },
          error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat menghapus berita.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            }
            showAlert(errorMessage, 'warning');
          }
        })
      });
    }

    // Search dengan debounce
    let _searchTimeout = null;
    $('#searchNews').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        currentSearch = q;
        loadNews(1, q, currentCategory, DEFAULT_LIMIT);
      }, 300);
    });

    // Category filter change
    $('#categoryFilter').on('change', function() {
      const categoryId = $(this).val();
      currentCategory = categoryId;
      loadNews(1, currentSearch, categoryId, DEFAULT_LIMIT);
    });

    // Reset filters button (opsional - bisa ditambahkan di HTML)
    window.resetNewsFilters = function() {
      resetFilters();
    };

    // Init 
    $(document).ready(function() {
      // Load categories untuk dropdown dan filter
      loadCategories();

      // initial load berita
      loadNews(1, '', '', DEFAULT_LIMIT);

      // Reset form when modal is closed
      $('#modal-add-news').on('hidden.bs.modal', function() {
        $('#form-add-news')[0].reset();
        $('.categories-select').val(null).trigger('change');
      });

      // Reset edit modal ketika ditutup
      $('#modal-edit-news').on('hidden.bs.modal', function() {
        const $select = $('#edit-categories');
        if ($select.hasClass('select2-hidden-accessible')) {
          $select.select2('destroy');
        }
        $select.empty();
      });

      // safety: ensure modals exist
      if (!document.getElementById('modal-alert')) {
        console.warn('Element #modal-alert tidak ditemukan. showAlert() membutuhkan modal ini.');
      }
      if (!document.getElementById('modal-confirm')) {
        console.warn('Element #modal-confirm tidak ditemukan. showConfirm() membutuhkan modal ini.');
      }
    });
  </script>

  <!--   Core JS Files   -->
  <script src="/cms_ivss/public/assets/js/core/popper.min.js"></script>
  <script src="/cms_ivss/public/assets/js/core/bootstrap.min.js"></script>
  <script src="/cms_ivss/public/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/cms_ivss/public/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="/cms_ivss/public/assets/js/plugins/chartjs.min.js"></script>

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="/cms_ivss/public/assets/js/argon-dashboard.min.js?v=2.1.0"></script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>