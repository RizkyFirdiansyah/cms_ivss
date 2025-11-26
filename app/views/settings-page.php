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

    <!-- Settings Content -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Pengaturan Global</h5>
                <button class="btn btn-sm btn-success mb-0" id="btn-save-settings">
                  <i class="fas fa-save me-1"></i> Simpan Semua Pengaturan
                </button>
              </div>
            </div>

            <div class="card-body p-0">
              <form id="form-settings" enctype="multipart/form-data">
                <!-- Site Identity Section -->
                <div class="card m-4">
                  <h6 class=" me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-globe me-2"></i>Identitas Situs</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6 w-100">
                        <div class="mb-3">
                          <label class="form-label">Nama Situs</label>
                          <input type="text" name="site_name" class="form-control" placeholder="LAB IVSS">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Logo Situs</label>
                          <input type="file" name="site_logo" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp,.svg">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP, SVG (Maks. 2MB)</div>
                          <div class="mt-2" id="logo-preview">
                            <!-- Logo preview akan diisi via JavaScript -->
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Favicon</label>
                          <input type="file" name="site_favicon" class="form-control" accept=".ico,.png,.jpg,.jpeg,.gif">
                          <div class="form-text text-xs">Format: ICO, PNG, JPG, GIF (Maks. 1MB)</div>
                          <div class="mt-2" id="favicon-preview">
                            <!-- Favicon preview akan diisi via JavaScript -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Contact Information Section -->
                <div class="card m-4">
                  <h6 class=" me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-address-book me-2"></i>Informasi Kontak</h6>
                  <!-- <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0"><i class="fas fa-address-book me-2"></i>Informasi Kontak</h6>
                  </div> -->
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Alamat</label>
                          <input type="text" name="contact_address" class="form-control" placeholder="Politeknik Negeri Malang">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Email</label>
                          <input type="email" name="contact_email" class="form-control" placeholder="info@ivsslab.ac.id">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Telepon</label>
                          <input type="text" name="contact_phone" class="form-control" placeholder="+62 xxx xxxx xxxx">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Footer Content Section -->
                <div class="card m-4">
                  <h6 class=" me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-shoe-prints me-2"></i>Konten Footer</h6>
                  <!-- <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0"><i class="fas fa-footer me-2"></i>Konten Footer</h6>
                  </div> -->
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label">Deskripsi Laboratorium</label>
                      <textarea name="footer_description" class="form-control" rows="4" placeholder="IVSS Laboratory adalah laboratorium yang mengintegrasikan computer vision, AI, dan IoT..."></textarea>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Teks Copyright</label>
                          <input type="text" name="footer_copyright" class="form-control" placeholder="© 2025 LAB IVSS.">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Social Media Section -->
                <div class="card m-4">
                  <h6 class=" me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-address-book me-2"></i>Media Sosial</h6>
                  <!-- <div class="card-header bg-gradient-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-share-alt me-2"></i>Media Sosial</h6>
                  </div> -->
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Facebook</label>
                          <input type="url" name="social_facebook" class="form-control" placeholder="https://facebook.com/ivsslab">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Instagram</label>
                          <input type="url" name="social_instagram" class="form-control" placeholder="https://instagram.com/ivsslab">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Twitter</label>
                          <input type="url" name="social_twitter" class="form-control" placeholder="https://twitter.com/ivsslab">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">LinkedIn</label>
                          <input type="url" name="social_linkedin" class="form-control" placeholder="https://linkedin.com/company/ivsslab">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">YouTube</label>
                          <input type="url" name="social_youtube" class="form-control" placeholder="https://youtube.com/ivsslab">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Legal Links Section -->
                <div class="card m-4">
                  <h6 class=" me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-address-book me-2"></i>Tautan Legal</h6>
                  <!-- <div class="card-header bg-gradient-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-gavel me-2"></i>Tautan Legal</h6>
                  </div> -->
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Disclaimer</label>
                          <input type="text" name="legal_disclaimer" class="form-control" placeholder="/disclaimer">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Privacy Policy</label>
                          <input type="text" name="legal_privacy_policy" class="form-control" placeholder="/privacy-policy">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Terms of Use</label>
                          <input type="text" name="legal_terms_of_use" class="form-control" placeholder="/terms-of-use">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Settings Content -->
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
      }, 1000);
    }

    // Function untuk menampilkan preview gambar yang sudah ada
    function showExistingImagePreviews(settings) {
      // Logo preview
      if (settings.site_logo && settings.site_logo.value) {
        $('#logo-preview').html(`
          <img src="${BASE_URL}/uploads/settings/${settings.site_logo.value}" alt="Current Logo" class="img-thumbnail" style="max-height: 80px;">
          <div class="form-text text-xs mt-1">Logo saat ini</div>
        `);
      } else {
        $('#logo-preview').html('<div class="text-muted text-xs">Belum ada logo</div>');
      }

      // Favicon preview
      if (settings.site_favicon && settings.site_favicon.value) {
        $('#favicon-preview').html(`
          <img src="${BASE_URL}/uploads/settings/${settings.site_favicon.value}" alt="Current Favicon" class="img-thumbnail" style="max-height: 80px;">
          <div class="form-text text-xs mt-1">Favicon saat ini</div>
        `);
      } else {
        $('#favicon-preview').html('<div class="text-muted text-xs">Belum ada favicon</div>');
      }
    }

    // Read Data
    function readData() {
      $.ajax({
        url: BASE_URL + '/settings/read',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          console.log('Settings data response:', res);
          if (res.success && res.data) {
            $('#form-settings input[name="site_name"]').val(res.data.site_name.value || '');
            $('#form-settings input[name="contact_address"]').val(res.data.contact_address.value || '');
            $('#form-settings input[name="contact_email"]').val(res.data.contact_email.value || '');
            $('#form-settings input[name="contact_phone"]').val(res.data.contact_phone.value || '');
            $('#form-settings textarea[name="footer_description"]').val(res.data.footer_description.value || '');
            $('#form-settings input[name="footer_copyright"]').val(res.data.footer_copyright.value || '');
            $('#form-settings input[name="social_facebook"]').val(res.data.social_facebook.value || '');
            $('#form-settings input[name="social_instagram"]').val(res.data.social_instagram.value || '');
            $('#form-settings input[name="social_twitter"]').val(res.data.social_twitter.value || '');
            $('#form-settings input[name="social_linkedin"]').val(res.data.social_linkedin.value || '');
            $('#form-settings input[name="social_youtube"]').val(res.data.social_youtube.value || '');
            $('#form-settings input[name="legal_disclaimer"]').val(res.data.legal_disclaimer.value || '');
            $('#form-settings input[name="legal_privacy_policy"]').val(res.data.legal_privacy_policy.value || '');
            $('#form-settings input[name="legal_terms_of_use"]').val(res.data.legal_terms_of_use.value || '');

            showExistingImagePreviews(res.data);

          } else {
            showAlert('Gagal memuat data pengaturan', 'error');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error details:', {
            xhr: xhr,
            status: status,
            error: error,
            responseText: xhr.responseText
          });

          let errorMessage = 'Terjadi kesalahan saat membaca data pengaturan.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'error');
        }
      });
    }

    // Save Settings
    $('#btn-save-settings').on('click', function() {
      const btn = $(this);
      const originalText = btn.html();

      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

      const formData = new FormData($('#form-settings')[0]);

      // Debug: Log semua data yang akan dikirim
      console.log('Form data yang akan dikirim:');
      for (let pair of formData.entries()) {
        console.log(pair[0] + ': ', pair[1]);
      }

      $.ajax({
        url: BASE_URL + '/settings/update',
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
              readData();
            }, 1000);
          } else {
            showAlert(res.message || 'Gagal menyimpan pengaturan.', 'error');
          }
        },
        error: function(xhr) {
          console.error('Save error:', xhr);
          let errorMessage = 'Terjadi kesalahan saat menyimpan pengaturan.';
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
      const previewContainer = input.siblings('.mt-2');

      if (this.files && this.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
          if (previewContainer.length === 0) {
            // Jika container preview belum ada, buat baru
            input.after('<div class="mt-2"><img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 80px;"></div>');
          } else {
            // Jika sudah ada, update gambar
            previewContainer.html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 80px;">');
          }
        }

        reader.readAsDataURL(this.files[0]);
      } else {
        // Jika file dihapus, tampilkan preview yang lama (jika ada)
        readData();
      }
    });

    // Initialize on document ready
    $(document).ready(function() {
      readData();

      // Handle form submit prevention
      $('#form-settings').on('submit', function(e) {
        e.preventDefault();
        $('#btn-save-settings').click();
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