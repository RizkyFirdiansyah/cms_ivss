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

    <!-- About Management Content -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Manajemen Konten Tentang Kami</h5>
                <div>
                  <button class="btn btn-sm btn-success mb-0" id="btn-save-about">
                    <i class="fas fa-save me-1"></i> Simpan
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body p-0">
              <form id="form-about" enctype="multipart/form-data">
                <!-- Header Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-heading me-2"></i>Header Section</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Judul Header</label>
                          <input type="text" name="about_header_title" class="form-control" placeholder="Tentang LAB IVSS">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Subjudul Header</label>
                          <input type="text" name="about_header_subtitle" class="form-control" placeholder="Mengenal lebih dekat laboratorium kami">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Gambar Background Header</label>
                          <input type="file" name="about_header_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</div>
                          <div class="mt-2" id="about-header-image-preview">
                            <!-- Header image preview akan diisi via JavaScript -->
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
                          <textarea name="about_profile_description" class="form-control" rows="5" placeholder="IVSS Laboratory adalah laboratorium yang mengintegrasikan computer vision, AI, dan IoT untuk menciptakan solusi inovatif..."></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Gambar Profil</label>
                          <input type="file" name="about_profile_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                          <div class="mt-2" id="about-profile-image-preview">
                            <!-- Profile image preview -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Vision & Mission Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-bullseye me-2"></i>Visi & Misi</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Judul Visi</label>
                          <input type="text" name="about_vision_title" class="form-control" placeholder="Visi Kami">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Judul Misi</label>
                          <input type="text" name="about_mission_title" class="form-control" placeholder="Misi Kami">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Konten Visi</label>
                          <textarea name="about_vision_content" class="form-control" rows="4" placeholder="Menjadi laboratorium penelitian terdepan dalam bidang computer vision, artificial intelligence, dan Internet of Things..."></textarea>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Konten Misi</label>
                          <textarea name="about_mission_content" class="form-control" rows="4" placeholder="1. Melakukan penelitian inovatif di bidang computer vision, AI, dan IoT&#10;2. Mengembangkan solusi teknologi yang aplikatif dan berdampak luas..."></textarea>
                          <div class="form-text text-xs">Gunakan enter untuk membuat poin baru</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Activities Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-tasks me-2"></i>Kegiatan & Proyek</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Judul Section Kegiatan</label>
                          <input type="text" name="activities_title" class="form-control" placeholder="Kegiatan & Proyek">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Subjudul Kegiatan</label>
                          <input type="text" name="activities_subtitle" class="form-control" placeholder="Berbagai kegiatan dan proyek penelitian yang sedang dikembangkan">
                        </div>
                      </div>
                    </div>

                    <!-- Activities Accordion -->
                    <div class="accordion" id="aboutActivitiesAccordion">
                      <!-- Activity 1 -->
                      <div class="accordion-item">
                        <div class="accordion-header d-flex align-items-center" id="aboutActivityHeading1">
                          <button class="accordion-button gap-3 d-flex justify-content-between align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#aboutActivityCollapse1" aria-expanded="true" aria-controls="aboutActivityCollapse1">
                            <i class="fas fa-project-diagram text-primary"></i>
                            <span id="about-activity-1-preview-title">Kegiatan 1</span>
                          </button>
                          <i class="fa-solid fa-angle-down "></i>
                        </div>
                        <div id="aboutActivityCollapse1" class="accordion-collapse collapse show" aria-labelledby="aboutActivityHeading1" data-bs-parent="#aboutActivitiesAccordion">
                          <div class="accordion-body">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Judul Kegiatan 1</label>
                                  <input type="text" name="activity_1_title" class="form-control about-activity-title-input" placeholder="Research Project 1" data-activity="1">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Gambar Kegiatan 1</label>
                                  <input type="file" name="activity_1_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                                  <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                                  <div class="mt-2" id="about-activity-1-image-preview">
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
                        <div class="accordion-header d-flex align-items-center" id="aboutActivityHeading2">
                          <button class="accordion-button gap-3 d-flex justify-content-between align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#aboutActivityCollapse2" aria-expanded="false" aria-controls="aboutActivityCollapse2">
                            <i class="fas fa-flask text-primary"></i>
                            <span id="about-activity-2-preview-title">Kegiatan 2</span>
                          </button>
                          <i class="fa-solid fa-angle-down"></i>
                        </div>
                        <div id="aboutActivityCollapse2" class="accordion-collapse collapse" aria-labelledby="aboutActivityHeading2" data-bs-parent="#aboutActivitiesAccordion">
                          <div class="accordion-body">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Judul Kegiatan 2</label>
                                  <input type="text" name="activity_2_title" class="form-control about-activity-title-input" placeholder="Research Project 2" data-activity="2">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Gambar Kegiatan 2</label>
                                  <input type="file" name="activity_2_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                                  <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                                  <div class="mt-2" id="about-activity-2-image-preview">
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
                        <div class="accordion-header d-flex align-items-center" id="aboutAactivityHeading3">
                          <button class="accordion-button gap-3 d-flex justify-content-between align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#aboutActivityCollapse3" aria-expanded="true" aria-controls="aboutActivityCollapse3">
                            <i class="fas fa-cogs text-primary "></i>
                            <span id="activity-3-preview-title">Kegiatan 3</span>
                          </button>
                          <i class="fa-solid fa-angle-down"></i>
                        </div>
                        <div id="aboutActivityCollapse3" class="accordion-collapse collapse" aria-labelledby="aboutActivityHeading3" data-bs-parent="#aboutActivitiesAccordion">
                          <div class="accordion-body">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Judul Kegiatan 3</label>
                                  <input type="text" name="activity_3_title" class="form-control about-activity-title-input" placeholder="Research Project 3" data-activity="3">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                  <label class="form-label">Gambar Kegiatan 3</label>
                                  <input type="file" name="activity_3_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                                  <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 2MB)</div>
                                  <div class="mt-2" id="about-activity-3-image-preview">
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
                    </div>
                    <!-- End Activities Accordion -->
                  </div>
                </div>

                <!-- Gallery Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-images me-2"></i>Galeri</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="alert alert-warning text-white">
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
                          <button type="button" class="btn btn-sm btn-outline-primary" id="btn-preview-about-gallery">
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
    <!-- End About Management Content -->
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
      const imageFields = [
        'about_header_image', 'about_profile_image', 'activity_1_image', 'activity_2_image', 'activity_3_image'
      ];

      imageFields.forEach(field => {
        const previewId = `${field.replace(/_/g, '-')}-preview`;
        const imagePath = getImagePath(contents, field);

        if (imagePath) {
          $(`#${previewId}`).html(`
          <img src="${BASE_URL}/uploads/about/${imagePath}" alt="Current ${field}" class="img-thumbnail" style="max-height: 120px;">
          <div class="form-text text-xs mt-1">Gambar saat ini</div>
        `);
        } else {
          $(`#${previewId}`).html('<div class="text-muted text-xs">Belum ada gambar</div>');
        }
      });
    }

    // Helper function untuk mendapatkan path gambar dari structured data
    function getImagePath(contents, field) {
      const fieldMap = {
        'about_header_image': contents.header?.image_path,
        'about_profile_image': contents.profile?.image_path,
        'activity_1_image': contents.activities?.items?.[0]?.image_path,
        'activity_2_image': contents.activities?.items?.[1]?.image_path,
        'activity_3_image': contents.activities?.items?.[2]?.image_path
      };

      return fieldMap[field] || '';
    }

    // Update activity title preview in real-time
    $(document).on('input', '.about-activity-title-input', function() {
      const activityNumber = $(this).data('activity');
      const title = $(this).val() || `Kegiatan ${activityNumber}`;
      $(`#about-activity-${activityNumber}-preview-title`).text(title);
    });

    // Preview Gallery Images
    $('#btn-preview-about-gallery').off('click').on('click', function() {
      loadAboutGalleryPreview();
    });

    // Load gallery images preview in vertical slider modal
    function loadAboutGalleryPreview() {
      const modalElement = document.getElementById('galleryModal');
      const myModal = bootstrap.Modal.getOrCreateInstance(modalElement);

      $.ajax({
        url: BASE_URL + '/about/gallery',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          const container = $('#gallery-slider-container');
          const indicators = $('#gallery-slider-indicators');

          // Bersihkan konten lama
          container.empty();
          indicators.empty();

          if (res.success && res.data && res.data.length > 0) {
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

    // Read About Data
    function readAboutData() {
      $.ajax({
        url: BASE_URL + '/about/read',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            const data = res.data;

            // Header Section
            $('#form-about input[name="about_header_title"]').val(data.header?.title || '');
            $('#form-about input[name="about_header_subtitle"]').val(data.header?.subtitle || '');

            // Profile Section
            $('#form-about textarea[name="about_profile_description"]').val(data.profile?.description || '');

            // Vision & Mission Section
            $('#form-about input[name="about_vision_title"]').val(data.vision_mission?.vision_title || '');
            $('#form-about textarea[name="about_vision_content"]').val(data.vision_mission?.vision_content || '');
            $('#form-about input[name="about_mission_title"]').val(data.vision_mission?.mission_title || '');
            $('#form-about textarea[name="about_mission_content"]').val(data.vision_mission?.mission_content || '');

            // Activities Section
            $('#form-about input[name="activities_title"]').val(data.activities?.title || '');
            $('#form-about input[name="activities_subtitle"]').val(data.activities?.subtitle || '');

            // Activities items
            if (data.activities?.items && data.activities.items.length >= 3) {
              $('#form-about input[name="activity_1_title"]').val(data.activities.items[0]?.title || '');
              $('#form-about textarea[name="activity_1_description"]').val(data.activities.items[0]?.description || '');
              $('#form-about input[name="activity_2_title"]').val(data.activities.items[1]?.title || '');
              $('#form-about textarea[name="activity_2_description"]').val(data.activities.items[1]?.description || '');
              $('#form-about input[name="activity_3_title"]').val(data.activities.items[2]?.title || '');
              $('#form-about textarea[name="activity_3_description"]').val(data.activities.items[2]?.description || '');
            }

            // Gallery Section
            $('#form-about input[name="gallery_title"]').val(data.gallery?.title || '');
            $('#form-about input[name="gallery_subtitle"]').val(data.gallery?.subtitle || '');

            // Show existing images
            showExistingImagePreviews(data);

            // Update activity titles preview
            for (let i = 1; i <= 3; i++) {
              const title = data.activities?.items?.[i - 1]?.title || `Kegiatan ${i}`;
              $(`#about-activity-${i}-preview-title`).text(title);
            }

          } else {
            showAlert('Gagal memuat data konten tentang kami', 'error');
          }
        },
        error: function(xhr, status, error) {
          let errorMessage = 'Terjadi kesalahan saat membaca data konten tentang kami.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'error');
        }
      });
    }

    // Save About Contents
    $('#btn-save-about').on('click', function() {
      const btn = $(this);
      const originalText = btn.html();

      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

      const formData = new FormData($('#form-about')[0]);

      $.ajax({
        url: BASE_URL + '/about/update',
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
              readAboutData();
            }, 1000);
          } else {
            showAlert(res.message || 'Gagal menyimpan konten tentang kami.', 'error');
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menyimpan konten tentang kami.';
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
        readAboutData();
      }
    });

    // Initialize on document ready
    $(document).ready(function() {
      readAboutData();

      // Handle form submit prevention
      $('#form-about').on('submit', function(e) {
        e.preventDefault();
        $('#btn-save-about').click();
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