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

    <!-- Home Management Content -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Manajemen Konten Home</h5>
                <div>
                  <button class="btn btn-sm btn-success mb-0" id="btn-save-home">
                    <i class="fas fa-save me-1"></i> Simpan Semua Konten
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body p-0">
              <form id="form-home" enctype="multipart/form-data">

                <!-- Hero Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-home me-2"></i>Hero Section</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Judul Hero</label>
                          <input type="text" name="hero_title" class="form-control" placeholder="Selamat Datang di LAB IVSS">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Subjudul Hero</label>
                          <input type="text" name="hero_subtitle" class="form-control" placeholder="Laboratorium Computer Vision, AI, dan IoT">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Gambar Background Hero</label>
                          <input type="file" name="hero_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</div>
                          <div class="mt-2" id="hero-image-preview">
                            <!-- Hero image preview akan diisi via JavaScript -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Profile Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-user-circle me-2"></i>Profil Singkat</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12">
                        <div class="mb-3">
                          <label class="form-label">Deskripsi Profil</label>
                          <textarea name="profile_description" class="form-control" rows="5" placeholder="IVSS Laboratory adalah laboratorium yang mengintegrasikan computer vision, AI, dan IoT untuk menciptakan solusi inovatif..."></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Gambar Profil 1</label>
                          <input type="file" name="profile_image_1" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</div>
                          <div class="mt-2" id="profile-image-1-preview">
                            <!-- Profile image 1 preview -->
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Gambar Profil 2</label>
                          <input type="file" name="profile_image_2" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</div>
                          <div class="mt-2" id="profile-image-2-preview">
                            <!-- Profile image 2 preview -->
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Gambar Profil 3</label>
                          <input type="file" name="profile_image_3" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</div>
                          <div class="mt-2" id="profile-image-3-preview">
                            <!-- Profile image 3 preview -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Activities Preview Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-tasks me-2"></i>Preview Kegiatan & Proyek</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="alert alert-warning text-white">
                        <i class="fas fa-info-circle me-2"></i>
                        Data kegiatan diambil dari <strong>Management Tentang Kami</strong>.
                        <a href="<?= BASE_URL ?>/about" class="alert-link">Edit kegiatan di sini</a>.
                      </div>
                      <div class="col-12">
                        <div id="activities-preview-container" class="row g-3">
                          <!-- Activities preview akan diisi via JavaScript -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Gallery Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-images me-2"></i>Galeri Preview</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="alert alert-warning text-white">
                        <i class="fas fa-info-circle me-2"></i>
                        Gambar galeri diambil secara otomatis dari <strong>Model Galeri</strong> (10 gambar terbaru)
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="mb-3">
                          <button type="button" class="btn btn-sm btn-outline-primary" id="btn-preview-gallery">
                            <i class="fas fa-eye me-1"></i> Preview Gambar Galeri
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
    <!-- End Home Management Content -->
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

  <!-- Gallery Preview Modal -->
  <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="galleryModalLabel">
            <i class="fas fa-images me-2"></i>Preview Galeri
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
            <!-- Indicators -->
            <div class="carousel-indicators" id="gallery-slider-indicators">
              <!-- Indicators will be loaded here -->
            </div>

            <!-- Slides -->
            <div class="carousel-inner" id="gallery-slider-container">
              <!-- Slides will be loaded here -->
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Gallery Preview Modal -->

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
      console.log('Setting old image values:', contents);

      const imageFields = [
        'hero_image', 'profile_image_1', 'profile_image_2', 'profile_image_3'
      ];

      imageFields.forEach(field => {
        const previewId = `${field.replace(/_/g, '-')}-preview`;
        const hiddenInputId = `old_${field}`;

        if (contents[field] && contents[field].value) {
          // Set nilai input hidden untuk file lama
          $(`#${hiddenInputId}`).val(contents[field].value);
          console.log(`Set ${hiddenInputId} to:`, contents[field].value);

          // Tampilkan preview gambar
          $(`#${previewId}`).html(`
            <img src="${BASE_URL}/uploads/home/${contents[field].value}" alt="Current ${field}" class="img-thumbnail" style="max-height: 120px;">
            <div class="form-text text-xs mt-1">Gambar saat ini</div>
            <input type="hidden" name="old_${field}" value="${contents[field].value}">
          `);
        } else {
          // Kosongkan input hidden jika tidak ada gambar
          $(`#${hiddenInputId}`).val('');
          console.log(`Cleared ${hiddenInputId}`);
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
        readHomeData();
      }
    });

    // Load activities preview
    function loadActivitiesPreview() {
      $.ajax({
        url: BASE_URL + '/home/activities-preview',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          const container = $('#activities-preview-container');
          container.empty();

          if (res.success && res.data && res.data.length > 0) {
            console.log(res);
            res.data.forEach((activity, index) => {
              container.append(`
                <div class="col-md-4">
                  <div class="card h-100">
                    ${activity.image ? `
                    <img src="${BASE_URL}/uploads/about/${activity.image}" class="p-0 card-img-top mb-2" style="height: 150px; object-fit: cover;" alt="${activity.title}">
                    ` : `
                    <div class="text-center text-muted py-4 bg-light rounded">
                    <i class="fas fa-image fa-2x mb-2"></i>
                    <p class="small mb-0">Belum ada gambar</p>
                    </div>
                    `}
                    <div class="card-body p-4 pt-2">
                      <h6 class="card-title text-sm">${activity.title || `Kegiatan ${index + 1}`}</h6>
                      <p class="card-text text-xs text-muted mb-0">${activity.description || 'Belum ada deskripsi'}</p>
                    </div>
                  </div>
                </div>
              `);
            });
          } else {
            container.html(`
              <div class="col-12">
                <div class="text-center text-muted py-4">
                  <i class="fas fa-tasks fa-2x mb-2"></i>
                  <h6 class="mb-2">Belum ada kegiatan</h6>
                  <p class="text-xs mb-0">Tambahkan kegiatan melalui menu <a href="${BASE_URL}/admin/about">Tentang Kami</a></p>
                </div>
              </div>
            `);
          }
        },
        error: function(xhr) {
          console.error('Error loading activities preview:', xhr);
          $('#activities-preview-container').html(`
            <div class="col-12">
              <div class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h6 class="mb-2">Gagal memuat data kegiatan</h6>
                <p class="text-xs mb-0">Terjadi kesalahan saat mengambil data</p>
              </div>
            </div>
          `);
        }
      });
    }

    // Preview Gallery Images
    $('#btn-preview-gallery').off('click').on('click', function() {
      loadGalleryPreview();
    });

    // Load gallery images preview in vertical slider modal
    function loadGalleryPreview() {
      const modalElement = document.getElementById('galleryModal');
      const myModal = bootstrap.Modal.getOrCreateInstance(modalElement);

      $.ajax({
        url: BASE_URL + '/home/gallery',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          const container = $('#gallery-slider-container');
          const indicators = $('#gallery-slider-indicators');

          // Bersihkan konten lama
          container.empty();
          indicators.empty();

          if (res.success && res.data && res.data.length > 0) {
            console.log(res);

            res.data.forEach((image, index) => {
              // Add indicator
              indicators.append(`
                <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="${index}" 
                    class="${index === 0 ? 'active' : ''}" aria-label="Slide ${index + 1}"></button>
              `);

              // Add slide
              container.append(`
                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                  <div class="d-flex flex-column align-items-center">
                    <img src="${BASE_URL}/uploads/gallery/${image.link}" class="d-block img-fluid rounded" style="max-height: 500px; object-fit: contain;" alt="${image.title || `Gambar ${index + 1}`}">
                    <div class="mt-3 text-center">
                      <h6 class="mb-1">${image.title || `Gambar ${index + 1}`}</h6>
                      ${image.description ? `<p class="text-sm text-muted mb-0">${image.description}</p>` : ''}
                    </div>
                  </div>
                </div>
              `);
            });

          } else {
            // Tampilan jika kosong
            container.html(`
              <div class="carousel-item active">
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                  <i class="fas fa-images fa-4x text-muted mb-3"></i>
                  <h5 class="text-muted">Belum ada gambar di galeri</h5>
                  <p class="text-sm text-muted">Upload gambar melalui menu Galeri terlebih dahulu</p>
                </div>
              </div>
            `);
          }

          // Tampilkan modal setelah konten dimuat
          myModal.show();
        },
        error: function(xhr) {
          console.error('Error loading gallery:', xhr);
          const container = $('#gallery-slider-container');
          container.html(`
            <div class="carousel-item active">
              <div class="d-flex flex-column align-items-center justify-content-center py-5">
                <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                <h5 class="text-danger">Gagal memuat data galeri</h5>
                <p class="text-sm text-muted">Terjadi kesalahan saat mengambil data</p>
              </div>
            </div>
          `);

          myModal.show();
        }
      });
    }

    // Read Home Data
    function readHomeData() {
      $.ajax({
        url: BASE_URL + '/home/read',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          console.log('Home contents response:', res);
          if (res.success && res.data) {
            // Hero Section
            $('#form-home input[name="hero_title"]').val(res.data.hero_title?.value || '');
            $('#form-home input[name="hero_subtitle"]').val(res.data.hero_subtitle?.value || '');

            // Profile Section
            $('#form-home textarea[name="profile_description"]').val(res.data.profile_description?.value || '');

            // Show existing images dan set input hidden
            showExistingImagePreviews(res.data);

            console.log('Data home berhasil dimuat ke form');
          } else {
            showAlert('Gagal memuat data konten home', 'error');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error details:', {
            xhr: xhr,
            status: status,
            error: error,
            responseText: xhr.responseText
          });

          let errorMessage = 'Terjadi kesalahan saat membaca data konten home.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'error');
        }
      });
    }

    // Save Home Contents - Debug version
    $('#btn-save-home').on('click', function() {
      const btn = $(this);
      const originalText = btn.html();

      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

      const formData = new FormData($('#form-home')[0]);

      // Debug: Log semua data yang akan dikirim
      console.log('Form data yang akan dikirim:');
      for (let pair of formData.entries()) {
        console.log(pair[0] + ': ', pair[1]);
      }

      $.ajax({
        url: BASE_URL + '/home/update',
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
              readHomeData();
              loadActivitiesPreview(); // Reload activities preview juga
            }, 1000);
          } else {
            showAlert(res.message || 'Gagal menyimpan konten home.', 'error');
          }
        },
        error: function(xhr) {
          console.error('Save error:', xhr);
          let errorMessage = 'Terjadi kesalahan saat menyimpan konten home.';
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
      readHomeData();
      loadActivitiesPreview(); // Auto-load activities preview

      // Handle form submit prevention
      $('#form-home').on('submit', function(e) {
        e.preventDefault();
        $('#btn-save-home').click();
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