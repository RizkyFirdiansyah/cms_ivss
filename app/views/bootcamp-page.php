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

    <!-- Bootcamp Page Management Content -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Manajemen Halaman Bootcamp</h5>
                <div>
                  <button class="btn btn-sm btn-success mb-0" id="btn-save-bootcamp-page">
                    <i class="fas fa-save me-1"></i> Simpan
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body p-0">
              <form id="form-bootcamp-page" enctype="multipart/form-data">
                <!-- Header Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-heading me-2"></i>Header Section</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Judul Header <span class="text-danger">*</span></label>
                          <input type="text" name="bootcamp_header_title" class="form-control" placeholder="Bootcamp" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Subjudul Header <span class="text-danger">*</span></label>
                          <input type="text" name="bootcamp_header_subtitle" class="form-control" placeholder="Program pelatihan intensif dan workshop" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Gambar Background Header</label>
                          <input type="file" name="bootcamp_header_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</div>
                          <div class="mt-2" id="bootcamp-header-image-preview">
                            <!-- Header image preview akan diisi via JavaScript -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Info Section -->
                <div class="card m-4">
                  <div class="card-body">
                    <div class="alert alert-warning text-white mb-0">
                      <i class="fas fa-info-circle me-2"></i>
                      <strong>Informasi:</strong> Konten bootcamp dikelola melalui <strong>Manajemen Bootcamp</strong>.
                      <a href="<?= BASE_URL ?>/bootcamp" class="alert-link">Kelola bootcamp di sini</a>.
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Bootcamp Page Management Content -->
  </main>

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

  <?php require_once 'includes/footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";

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
      }, 3000);
    }

    // Function untuk menampilkan preview gambar yang sudah ada
    function showExistingImagePreviews(contents) {
      const imageFields = ['bootcamp_header_image'];

      imageFields.forEach(field => {
        const previewId = `${field.replace(/_/g, '-')}-preview`;
        if (contents[field] && contents[field].value) {
          $(`#${previewId}`).html(`
            <div class="d-flex align-items-start gap-3">
              <img src="${BASE_URL}/uploads/bootcamp-page/${contents[field].value}" 
                   alt="Current ${field}" 
                   class="img-thumbnail" 
                   style="max-height: 120px; max-width: 200px;">
              <div class="flex-grow-1">
                <div class="form-text text-xs mt-1">Gambar saat ini</div>
                <div class="text-xs text-muted">${contents[field].value}</div>
              </div>
            </div>
          `);
        } else {
          $(`#${previewId}`).html(`
            <div class="text-muted text-xs p-3 border rounded text-center">
              <i class="fas fa-image fa-lg mb-2"></i><br>
              Belum ada gambar
            </div>
          `);
        }
      });
    }

    // Read Bootcamp Page Data
    function readBootcampPageData() {
      $.ajax({
        url: BASE_URL + '/bootcamp-page/read',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            // Header Section
            $('#form-bootcamp-page input[name="bootcamp_header_title"]').val(res.data.header?.title || '');
            $('#form-bootcamp-page input[name="bootcamp_header_subtitle"]').val(res.data.header?.subtitle || '');

            // Show existing images
            if (res.data.header) {
              const contents = {
                bootcamp_header_image: {
                  value: res.data.header?.image_path || ''
                }
              };
              showExistingImagePreviews(contents);
            }

            showAlert('Data halaman bootcamp berhasil dimuat', 'success');
          } else {
            showAlert('Gagal memuat data konten halaman bootcamp', 'error');
          }
        },
        error: function(xhr, status, error) {
          let errorMessage = 'Terjadi kesalahan saat membaca data konten halaman bootcamp.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'error');
        }
      });
    }

    // Save Bootcamp Page Contents
    $('#btn-save-bootcamp-page').on('click', function() {
      const btn = $(this);
      const originalText = btn.html();

      // Validasi form
      const title = $('#form-bootcamp-page input[name="bootcamp_header_title"]').val();
      const subtitle = $('#form-bootcamp-page input[name="bootcamp_header_subtitle"]').val();

      if (!title.trim() || !subtitle.trim()) {
        showAlert('Judul dan Subjudul Header wajib diisi', 'warning');
        return;
      }

      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

      const formData = new FormData($('#form-bootcamp-page')[0]);

      // Kirim semua data sekaligus
      $.ajax({
        url: BASE_URL + '/bootcamp-page/update',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            showAlert(res.message, 'success');
            // Reload data setelah berhasil update
            setTimeout(() => {
              readBootcampPageData();
            }, 1000);
          } else {
            showAlert(res.message || 'Gagal menyimpan konten halaman bootcamp.', 'error');
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menyimpan konten halaman bootcamp.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'error');
        },
        complete: function() {
          btn.prop('disabled', false).html(originalText);
        }
      });
    });

    // File input preview untuk upload baru
    $('input[type="file"]').on('change', function() {
      const input = $(this);
      const fieldName = input.attr('name');
      const previewId = `${fieldName.replace(/_/g, '-')}-preview`;

      if (this.files && this.files[0]) {
        const file = this.files[0];
        
        // Validasi ukuran file (5MB)
        if (file.size > 5 * 1024 * 1024) {
          showAlert('Ukuran file maksimal 5MB', 'warning');
          input.val('');
          return;
        }

        // Validasi tipe file
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
          showAlert('Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP', 'warning');
          input.val('');
          return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
          $(`#${previewId}`).html(`
            <div class="d-flex align-items-start gap-3">
              <img src="${e.target.result}" class="img-thumbnail" style="max-height: 120px; max-width: 200px;">
              <div class="flex-grow-1">
                <div class="form-text text-xs mt-1">Preview gambar baru</div>
                <div class="text-xs text-muted">${file.name}</div>
                <div class="text-xs text-muted">${(file.size / 1024).toFixed(2)} KB</div>
              </div>
            </div>
          `);
        }

        reader.readAsDataURL(file);
      } else {
        // Jika file dihapus, tampilkan preview yang lama (jika ada)
        readBootcampPageData();
      }
    });

    // Initialize on document ready
    $(document).ready(function() {
      // Load initial data
      readBootcampPageData();

      // Handle form submit prevention
      $('#form-bootcamp-page').on('submit', function(e) {
        e.preventDefault();
        $('#btn-save-bootcamp-page').click();
      });

      // Keyboard shortcut untuk save (Ctrl + S)
      $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
          e.preventDefault();
          $('#btn-save-bootcamp-page').click();
        }
      });

      // Clear preview ketika form direset
      $('#form-bootcamp-page').on('reset', function() {
        setTimeout(() => {
          readBootcampPageData();
        }, 100);
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