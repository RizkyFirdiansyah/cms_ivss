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

    <!-- Galeri -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Galeri</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari gambar..." id="searchGallery">
              </div>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-gallery" data-bs-toggle="modal" data-bs-target="#modal-add-gallery">
                <i class="fa fa-plus"></i> Tambah Gambar
              </button>
            </div>
            <div class="row g-3 my-3" id="gallery-container"></div>
            <div id="pagination" class="mt-4 text-center"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Galeri -->
  </main>

  <!-- Modal: Tambah Gambar -->
  <div class="modal fade" id="modal-add-gallery" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-add-gallery" class="modal-content" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Gambar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="add-gallery-alert"></div>
          <div class="mb-3">
            <label class="form-label">Judul</label>
            <input name="title" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-control-label">Gambar</label>
            <input class="form-control" type="file" name="link" accept="image/*" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  <!-- End Modal: Tambah Gambar -->

  <!-- Modal Edit Gambar -->
  <div class="modal fade" id="modal-edit-gallery" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-edit-gallery" class="modal-content" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="old_file_path" id="edit-old-file-path">

        <div class="modal-header">
          <h5 class="modal-title">Edit Gambar</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div id="edit-gallery-alert"></div>
          <div class="mb-3">
            <label class="form-label">Judul</label>
            <input name="title" id="edit-title" class="form-control" required />
          </div>
          <div class="text-center">
            <img id="edit-preview" class="img-fluid rounded mb-2" style="max-height: 200px;">
          </div>
          <div class="mb-3">
            <label class="form-label">Gambar Baru (opsional)</label>
            <input type="file" name="link" class="form-control" accept="image/*">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn bg-gradient-primary" type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </div>

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

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";
    const DEFAULT_LIMIT = 8;
    let currentPage = 1;

    // Javascript Function Helpers
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

    // Render gallery card
    function renderGalleryCard(g) {
      const safeTitle = g.title ? g.title.replace(/"/g, '&quot;') : '';

      return `
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="card border-0 shadow-xs overflow-hidden h-100 rounded-4">
            <img src="${BASE_URL}/uploads/gallery/${g.link}" class="card-img-top object-fit-cover" style="height: 160px;" alt="${g.title}">
            <div class="card-body p-3">
              <h6 class="mb-1 fw-bold">${g.title}</h6>
              <div class="d-flex gap-2">
                <button class="btn mb-0 px-3 btn-warning btn-sm text-xs"
                  onclick="showEditGallery(this)"
                  data-id="${g.id}"
                  data-title="${safeTitle}"
                  data-link="${g.link}">
                  <i class="fa fa-edit"></i>
                </button>
                <button class="btn mb-0 px-3 btn-danger btn-sm text-xs"
                  onclick="deleteGallery(${g.id})">
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        `;
    }

    // Load gallery
    function loadGallery(page = 1, search = '', limit = DEFAULT_LIMIT) {
      const container = $('#gallery-container');
      const pagination = $('#pagination');
      container.html(`<div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</div>`);
      pagination.empty();

      $.ajax({
        url: BASE_URL + '/galeri/list',
        method: 'GET',
        dataType: 'json',
        data: {
          page: page,
          limit: limit,
          search: search
        },
        success: function(res) {
          container.empty();
          if (!res || !res.data || res.data.length === 0) {
            container.append(`<div class="col-12 text-center text-muted">Tidak ada data gambar.</div>`);
            return;
          }

          // Render cards
          res.data.forEach(g => container.append(renderGalleryCard(g)));
          console.log('Gallery data loaded:', res);

          // Pagination
          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';
          for (let i = 1; i <= totalPages; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 py-2 ${cls} mx-1" onclick="loadGallery(${i}, '${encodeURIComponent(search)}', ${limit})">${i}</button>`;
          }
          pagination.html(html);
          currentPage = page;
        },
        error: function(xhr, status, err) {
          console.error('Error loading gallery:', status, err);
          container.html(`<div class="col-12 text-center text-danger">Gagal memuat data. Periksa konsol.</div>`);
        }
      });
    }

    // Add gallery
    $("#form-add-gallery").on("submit", function(e) {
      e.preventDefault();
      const fd = new FormData(this);

      $.ajax({
        url: BASE_URL + "/galeri/create",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-add-gallery").modal("hide");
            $("#form-add-gallery")[0].reset();
            loadGallery(currentPage);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error adding gallery:', error);
          showAlert('Terjadi kesalahan saat menambah gambar', 'error');
        }
      });
    });

    // Show edit gallery
    window.showEditGallery = function(el) {
      const $el = $(el);
      console.log('Edit button clicked:', $el);
      const id = $el.data('id');
      const title = $el.data('title');
      const file_name = $el.data('link');

      console.log('Editing gallery item:', {
        id,
        title,
        file_name
      });

      $('#edit-id').val(id);
      $('#edit-title').val(title);
      $('#edit-old-file-path').val(file_name);

      // Set preview image
      const imageUrl = `${BASE_URL}/uploads/gallery/${file_name}`;
      console.log('Image URL:', imageUrl);
      $('#edit-preview').attr('src', imageUrl).show();

      // Reset file input
      $('#form-edit-gallery input[type="file"]').val('');

      const modal = new bootstrap.Modal($('#modal-edit-gallery')[0]);
      modal.show();
    };

    // Update gallery
    $("#form-edit-gallery").on("submit", function(e) {
      e.preventDefault();
      const fd = new FormData(this);

      // Debug FormData
      console.log('Form elements:');
      for (let [key, value] of fd.entries()) {
        if (key === 'link' && value instanceof File) {
          console.log(`${key}:`, value.name, value.size, value.type);
        } else {
          console.log(`${key}:`, value);
        }
      }

      $.ajax({
        url: BASE_URL + "/galeri/update",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          console.log('Update response:', res);
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-edit-gallery").modal("hide");
            loadGallery(currentPage);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error updating gallery:', error);
          console.error('Status:', status);
          console.error('Response:', xhr.responseText);
          showAlert('Terjadi kesalahan saat memperbarui gambar', 'error');
        }
      });
    });

    // Delete gallery
    function deleteGallery(id) {
      showConfirm("Yakin ingin menghapus gambar ini?", function(ok) {
        if (!ok) return;

        $.ajax({
          url: BASE_URL + "/galeri/delete",
          method: "POST",
          data: {
            id: id
          },
          dataType: "json",
          success: function(res) {
            showAlert(res.message, res.success ? "success" : "error");
            if (res.success) loadGallery(currentPage);
          },
          error: function(xhr, status, error) {
            console.error('Error deleting gallery:', error);
            showAlert('Terjadi kesalahan saat menghapus gambar', 'error');
          }
        });
      });
    }

    // Search
    let _searchTimeout = null;
    $('#searchGallery').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        loadGallery(1, q, DEFAULT_LIMIT);
      }, 300);
    });

    // Preview image when file is selected in edit modal
    $('#form-edit-gallery input[type="file"]').on('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#edit-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(file);
      }
    });

    // Reset modals when closed
    $('#modal-add-gallery').on('hidden.bs.modal', function() {
      $('#form-add-gallery')[0].reset();
    });

    $('#modal-edit-gallery').on('hidden.bs.modal', function() {
      $('#form-edit-gallery')[0].reset();
    });

    // Init
    $(document).ready(function() {
      // initial load
      loadGallery(1, '', DEFAULT_LIMIT);
    });
  </script>

  <!-- Core JS Files -->
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
  <script src="/cms_ivss/public/assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>