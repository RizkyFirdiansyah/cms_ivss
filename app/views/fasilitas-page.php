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

    <!-- Facility Management Content -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Manajemen Halaman Fasilitas</h5>
                <div>
                  <button class="btn btn-sm btn-success mb-0" id="btn-save-facility">
                    <i class="fas fa-save me-1"></i> Simpan Konten
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body p-0">
              <form id="form-facility" enctype="multipart/form-data">
                <!-- Input hidden untuk file lama -->
                <input type="hidden" name="old_facility_header_image" id="old_facility_header_image" value="">

                <!-- Header Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-building me-2"></i>Header Section</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Judul Header</label>
                          <input type="text" name="facility_header_title" class="form-control" placeholder="Fasilitas Laboratorium">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Subjudul Header</label>
                          <input type="text" name="facility_header_subtitle" class="form-control" placeholder="Fasilitas lengkap untuk mendukung penelitian dan pembelajaran">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Gambar Background Header</label>
                          <input type="file" name="facility_header_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</div>
                          <div class="mt-2" id="facility-header-image-preview">
                            <!-- Header image preview akan diisi via JavaScript -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Facilities Preview Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-flask me-2"></i>Preview Fasilitas</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="alert alert-warning text-white">
                        <i class="fas fa-info-circle me-2"></i>
                        Data fasilitas diambil secara otomatis dari <strong>Management Fasilitas</strong>.
                        <a href="<?= BASE_URL ?>/fasilitas" class="alert-link">Kelola fasilitas di sini</a>.
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="mb-3">
                          <button type="button" class="btn btn-sm btn-outline-primary" id="btn-preview-facilities">
                            <i class="fas fa-eye me-1"></i> Preview Daftar Fasilitas
                          </button>
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
    <!-- End Facility Management Content -->
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

  <!-- Facilities Preview Modal -->
  <div class="modal fade" id="facilitiesModal" tabindex="-1" aria-labelledby="facilitiesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="facilitiesModalLabel">
            <i class="fas fa-flask me-2"></i>Preview Fasilitas
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row" id="facilities-preview-container">
            <!-- Facilities preview akan diisi via JavaScript -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Facilities Preview Modal -->

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
    function showExistingImagePreviews(contents) {

      const imageFields = ['facility_header_image'];

      imageFields.forEach(field => {
        const previewId = `${field.replace(/_/g, '-')}-preview`;
        const hiddenInput = $(`input[name="old_${field}"]`);

        if (contents[field] && contents[field].value) {
          // Set nilai input hidden untuk file lama
          hiddenInput.val(contents[field].value);

          // Tampilkan preview gambar
          $(`#${previewId}`).html(`
            <img src="${BASE_URL}/uploads/facility/${contents[field].value}" alt="Current ${field}" class="img-thumbnail" style="max-height: 120px;">
            <div class="form-text text-xs mt-1">Gambar saat ini</div>
          `);
        } else {
          // Kosongkan input hidden jika tidak ada gambar
          hiddenInput.val('');
          $(`#${previewId}`).html('<div class="text-muted text-xs">Belum ada gambar</div>');
        }
      });
    }

    // File input preview untuk upload baru
    $('input[type="file"]').on('change', function() {
      const input = $(this);
      const fieldName = input.attr('name');
      const previewId = `${fieldName.replace(/_/g, '-')}-preview`;

      if (this.files && this.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
          $(`#${previewId}`).html(`
            <img src="${e.target.result}" class="img-thumbnail" style="max-height: 120px;">
            <div class="form-text text-xs mt-1">Preview gambar baru</div>
          `);
        }

        reader.readAsDataURL(this.files[0]);
      } else {
        // Jika file dihapus, tampilkan preview yang lama (jika ada)
        readFacilityData();
      }
    });

    function truncateText(text, maxLength = 80) {
      if (!text) return '';
      if (text.length <= maxLength) {
        return text;
      }
      // Potong teks dan tambahkan elipsis
      return text.substring(0, maxLength) + '...';
    }

    function loadFacilitiesPreview() {
      const modalElement = $('#facilitiesModal');
      const myModal = bootstrap.Modal.getOrCreateInstance(modalElement);

      $.ajax({
        url: BASE_URL + '/fasilitas-page/allFasilitas',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          const container = $('#facilities-preview-container');
          container.empty();

          if (res.success) {
            res.data.forEach((facility, index) => {
              const shortContent = truncateText(facility.description, 100);

              container.append(`
                        <div class="col-lg-6 mb-4">
                            <div class="card border-0 shadow-sm facility-card">
                                ${facility.photo ? `
                                    <img src="${BASE_URL}/uploads/facility/${facility.photo}" 
                                        class="card-img-top" 
                                        style="height: 150px; object-fit: cover;" 
                                        alt="${facility.name}">
                                ` : `
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                        style="height: 150px;">
                                        <i class="fas fa-flask fa-3x text-muted"></i>
                                    </div>
                                `}
                                <div class="card-body px-3 py-1">
                                        <h6 class="card-title m-0 fw-bold text-dark">${facility.name}</h6>
                                    
                                    ${facility.description ? `
                                        <p class="card-text text-xs text-muted">${shortContent}</p>
                                    ` : '<p class="card-text text-xs text-muted">Tidak ada deskripsi</p>'}
                                </div>
                                <div class="card-footer p-2 mx-2">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        ${facility.created_at ? new Date(facility.created_at).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}
                                    </small>
                                </div>
                            </div>
                        </div>
                    `);
            });

          } else {
            container.html(`
                    <div class="col-12">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-flask fa-3x mb-3"></i>
                            <h5 class="mb-2">Belum ada fasilitas</h5>
                            <p class="text-sm mb-0">Tambahkan fasilitas melalui menu <a href="${BASE_URL}/admin/facilities" class="text-primary">Facility Management</a></p>
                        </div>
                    </div>
                `);
          }
          // Tampilkan modal setelah konten dimuat
          myModal.show();
        },
        error: function(xhr) {
          console.error('Error loading facilities:', xhr);
          const container = $('#facilities-preview-container');
          container.html(`
                <div class="col-12">
                    <div class="text-center text-danger py-5">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h5 class="mb-2">Gagal memuat data fasilitas</h5>
                        <p class="text-sm mb-0">Terjadi kesalahan saat mengambil data</p>
                    </div>
                </div>
            `);

          const modalElement = document.getElementById('facilitiesModal');
          const myModal = bootstrap.Modal.getOrCreateInstance(modalElement);
          myModal.show();
        }
      });
    }

    // Preview Facilities
    $('#btn-preview-facilities').off('click').on('click', function() {
      loadFacilitiesPreview();
    });

    // Read Facility Data
    function readFacilityData() {
      $.ajax({
        url: BASE_URL + '/fasilitas-page/read',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            // Header Section
            $('#form-facility input[name="facility_header_title"]').val(res.data.facility_header_title?.value || '');
            $('#form-facility input[name="facility_header_subtitle"]').val(res.data.facility_header_subtitle?.value || '');

            // Show existing images
            showExistingImagePreviews(res.data);
          } else {
            showAlert('Gagal memuat data konten fasilitas', 'error');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error details:', {
            xhr: xhr,
            status: status,
            error: error,
            responseText: xhr.responseText
          });

          let errorMessage = 'Terjadi kesalahan saat membaca data konten fasilitas.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'error');
        }
      });
    }

    // Save Facility Contents
    $('#btn-save-facility').on('click', function() {
      const btn = $(this);
      const originalText = btn.html();

      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

      const formData = new FormData($('#form-facility')[0]);

      $.ajax({
        url: BASE_URL + '/fasilitas-page/update',
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
              readFacilityData();
            }, 1000);
          } else {
            showAlert(res.message || 'Gagal menyimpan konten fasilitas.', 'error');
          }
        },
        error: function(xhr) {
          console.error('Save error:', xhr);
          let errorMessage = 'Terjadi kesalahan saat menyimpan konten fasilitas.';
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

    // Initialize on document ready
    $(document).ready(function() {
      readFacilityData();

      // Handle form submit prevention
      $('#form-facility').on('submit', function(e) {
        e.preventDefault();
        $('#btn-save-facility').click();
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