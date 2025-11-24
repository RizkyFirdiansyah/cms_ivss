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
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
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
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                          <div class="mt-2" id="profile-image-1-preview">
                            <!-- Profile image 1 preview -->
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Gambar Profil 2</label>
                          <input type="file" name="profile_image_2" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                          <div class="mt-2" id="profile-image-2-preview">
                            <!-- Profile image 2 preview -->
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-3">
                          <label class="form-label">Gambar Profil 3</label>
                          <input type="file" name="profile_image_3" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                          <div class="mt-2" id="profile-image-3-preview">
                            <!-- Profile image 3 preview -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Activities Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-tasks me-2"></i>Kegiatan & Proyek</h6>
                  <div class="card-body">
                    <!-- Activities Accordion -->
                    <div class="accordion" id="activitiesAccordion">
                      <!-- Activity 1 -->
                      <div class="accordion-item">
                        <div class="accordion-header d-flex align-items-center" id="activityHeading1">
                          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#activityCollapse1" aria-expanded="true" aria-controls="activityCollapse1">
                            <i class="fas fa-project-diagram me-2 text-primary"></i>
                            <span id="activity-1-preview-title">Kegiatan 1</span>
                          </button>
                          <i class="fa-solid fa-angle-down"></i>
                        </div>
                        <div id="activityCollapse1" class="accordion-collapse collapse show" aria-labelledby="activityHeading1" data-bs-parent="#activitiesAccordion">
                          <div class="accordion-body">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Judul Kegiatan 1</label>
                                  <input type="text" name="activity_1_title" class="form-control activity-title-input" placeholder="Research Project 1" data-activity="1">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Gambar Kegiatan 1</label>
                                  <input type="file" name="activity_1_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                                  <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                                  <div class="mt-2" id="activity-1-image-preview">
                                    <!-- Activity 1 image preview -->
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-12">
                                <div class="mb-3">
                                  <label class="form-label">Deskripsi Kegiatan 1</label>
                                  <textarea name="activity_1_description" class="form-control" rows="3" placeholder="Deskripsi kegiatan proyek pertama..."></textarea>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Activity 2 -->
                      <div class="accordion-item">
                        <div class="accordion-header d-flex align-items-center" id="activityHeading2">
                          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#activityCollapse2" aria-expanded="true" aria-controls="activityCollapse2">
                            <i class="fas fa-flask me-2 text-primary"></i>
                            <span id="activity-2-preview-title">Kegiatan 2</span>
                          </button>
                          <i class="fa-solid fa-angle-down"></i>
                        </div>
                        <div id="activityCollapse2" class="accordion-collapse collapse" aria-labelledby="activityHeading2" data-bs-parent="#activitiesAccordion">
                          <div class="accordion-body">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Judul Kegiatan 2</label>
                                  <input type="text" name="activity_2_title" class="form-control activity-title-input" placeholder="Research Project 2" data-activity="2">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Gambar Kegiatan 2</label>
                                  <input type="file" name="activity_2_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                                  <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                                  <div class="mt-2" id="activity-2-image-preview">
                                    <!-- Activity 2 image preview -->
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-12">
                                <div class="mb-3">
                                  <label class="form-label">Deskripsi Kegiatan 2</label>
                                  <textarea name="activity_2_description" class="form-control" rows="3" placeholder="Deskripsi kegiatan proyek kedua..."></textarea>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Activity 3 -->
                      <div class="accordion-item">
                        <div class="accordion-header d-flex align-items-center" id="activityHeading3">
                          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#activityCollapse3" aria-expanded="true" aria-controls="activityCollapse3">
                            <i class="fas fa-cogs me-2 text-primary"></i>
                            <span id="activity-3-preview-title">Kegiatan 3</span>
                          </button>
                          <i class="fa-solid fa-angle-down"></i>
                        </div>
                        <div id="activityCollapse3" class="accordion-collapse collapse" aria-labelledby="activityHeading3" data-bs-parent="#activitiesAccordion">
                          <div class="accordion-body">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Judul Kegiatan 3</label>
                                  <input type="text" name="activity_3_title" class="form-control activity-title-input" placeholder="Research Project 3" data-activity="3">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Gambar Kegiatan 3</label>
                                  <input type="file" name="activity_3_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                                  <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                                  <div class="mt-2" id="activity-3-image-preview">
                                    <!-- Activity 3 image preview -->
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-12">
                                <div class="mb-3">
                                  <label class="form-label">Deskripsi Kegiatan 3</label>
                                  <textarea name="activity_3_description" class="form-control" rows="3" placeholder="Deskripsi kegiatan proyek ketiga..."></textarea>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- End Activities Accordion -->
                    </div>
                  </div>
                </div>
            </div>

            <!-- Gallery Section -->
            <div class="card m-4">
              <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-images me-2"></i>Galeri Preview</h6>
              <div class="card-body">
                <div class="row">
                  <div class="alert alert-warning text-white ">
                    <i class="fas fa-info-circle me-2"></i>
                    Gambar galeri diambil secara otomatis dari <strong>Model Galeri</strong> (10 gambar terbaru)
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Judul Galeri</label>
                      <input type="text" name="gallery_title" class="form-control" placeholder="Galeri Kegiatan">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Subjudul Galeri</label>
                      <input type="text" name="gallery_subtitle" class="form-control" placeholder="Dokumentasi berbagai kegiatan laboratorium">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="mb-3">
                      <button type="button" class="btn btn-sm btn-outline-primary" id="btn-preview-gallery">
                        <i class="fas fa-eye me-1"></i> Preview Gambar Galeri
                      </button>
                    </div>
                    <div id="gallery-preview-container" class="row g-2">
                      <!-- Gallery preview akan diisi via JavaScript -->
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

  <!-- Gallery Preview Modal with Vertical Slider -->
  <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-md">
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
              <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
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
      const imageFields = [
        'hero_image', 'profile_image_1', 'profile_image_2', 'profile_image_3',
        'activity_1_image', 'activity_2_image', 'activity_3_image'
      ];

      imageFields.forEach(field => {
        const previewId = `${field.replace(/_/g, '-')}-preview`;
        if (contents[field] && contents[field].value) {
          $(`#${previewId}`).html(`
            <img src="${contents[field].value}" alt="Current ${field}" class="img-thumbnail" style="max-height: 120px;">
            <div class="form-text text-xs mt-1">Gambar saat ini</div>
          `);
        } else {
          $(`#${previewId}`).html('<div class="text-muted text-xs">Belum ada gambar</div>');
        }
      });
    }

    // Update activity title preview in real-time 
    $(document).on('input', '.activity-title-input', function() {
      const activityNumber = $(this).data('activity');
      const title = $(this).val() || `Kegiatan ${activityNumber}`;
      $(`#activity-${activityNumber}-preview-title`).text(title);
    });

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
              container.append(`<div class="carousel-item ${index === 0 ? 'active' : ''}">
                                  <div class="d-flex flex-column align-items-center">
                                    <img src="${BASE_URL}/uploads/gallery/${image.link}" class="d-block img-fluid rounded" style="max-height: 500px; object-fit: contain;"  alt="${image.title || `Gambar ${index + 1}`}">
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

            // Activities Section
            $('#form-home input[name="activity_title"]').val(res.data.activity_title?.value || '');
            $('#form-home input[name="activity_1_title"]').val(res.data.activity_1_title?.value || '');
            $('#form-home textarea[name="activity_1_description"]').val(res.data.activity_1_description?.value || '');
            $('#form-home input[name="activity_2_title"]').val(res.data.activity_2_title?.value || '');
            $('#form-home textarea[name="activity_2_description"]').val(res.data.activity_2_description?.value || '');
            $('#form-home input[name="activity_3_title"]').val(res.data.activity_3_title?.value || '');
            $('#form-home textarea[name="activity_3_description"]').val(res.data.activity_3_description?.value || '');

            // Gallery Section
            $('#form-home input[name="gallery_title"]').val(res.data.gallery_title?.value || '');
            $('#form-home input[name="gallery_subtitle"]').val(res.data.gallery_subtitle?.value || '');

            // Show existing images
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

    // Save Home Contents
    $('#btn-save-home').on('click', function() {
      const btn = $(this);
      const originalText = btn.html();

      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

      const formData = new FormData($('#form-home')[0]);

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

    // Initialize on document ready
    $(document).ready(function() {
      readHomeData();

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