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

    <!-- SOP Management Content -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Manajemen Halaman SOP</h5>
                <div>
                  <button class="btn btn-sm btn-success mb-0" id="btn-save-sop">
                    <i class="fas fa-save me-1"></i> Simpan
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body p-0">
              <form id="form-sop" enctype="multipart/form-data">
                <!-- Header Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-heading me-2"></i>Header Section</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Judul Header</label>
                          <input type="text" name="sop_header_title" class="form-control" placeholder="Standard Operating Procedure">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Subjudul Header</label>
                          <input type="text" name="sop_header_subtitle" class="form-control" placeholder="Prosedur operasional standar laboratorium">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Gambar Background Header</label>
                          <input type="file" name="sop_header_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</div>
                          <div class="mt-2" id="sop-header-image-preview">
                            <!-- Header image preview akan diisi via JavaScript -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Layanan -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-building me-2"></i>Layanan</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Lokasi</label>
                          <input type="text" name="sop_layanan_location" class="form-control" placeholder="Lokasi Layanan">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Email</label>
                          <input type="text" name="sop_layanan_email" class="form-control" placeholder="Email Layanan">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Jam Layanan</label>
                          <input type="text" name="sop_layanan_hours" class="form-control" placeholder="Jam Operasional Layanan">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            <!-- SOP Items Section -->
            <div class="card m-4">
              <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-list-alt me-2"></i>Item SOP & Layanan</h6>
              <div class="card-body">
                <!-- SOP Items Accordion -->
                <div class="accordion" id="sopItemsAccordion">
                  <!-- SOP Item 1 -->
                  <div class="accordion-item">
                    <div class="accordion-header d-flex align-items-center" id="sopItemHeading1">
                      <button class="accordion-button gap-3 d-flex justify-content-between align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sopItemCollapse1" aria-expanded="true" aria-controls="sopItemCollapse1">
                        <i class="fas fa-file-alt text-primary"></i>
                        <span id="sop-item-1-preview-title">SOP 1</span>
                      </button>
                      <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div id="sopItemCollapse1" class="accordion-collapse collapse show" aria-labelledby="sopItemHeading1" data-bs-parent="#sopItemsAccordion">
                      <div class="accordion-body">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Judul SOP 1</label>
                              <input type="text" name="sop_1_title" class="form-control sop-item-title-input" placeholder="Contoh: SOP Penggunaan Lab" data-item="1">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Link Dokumen SOP</label>
                              <input type="url" name="sop_1_document_link" class="form-control" placeholder="https://drive.google.com/...">
                              <div class="form-text text-xs">Link Google Drive untuk dokumen SOP lengkap</div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-12">
                            <div class="mb-3">
                              <label class="form-label">Deskripsi SOP 1</label>
                              <textarea name="sop_1_description" class="form-control" rows="3" placeholder="Deskripsi singkat tentang SOP ini..."></textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- SOP Item 2 -->
                  <div class="accordion-item">
                    <div class="accordion-header d-flex align-items-center" id="sopItemHeading2">
                      <button class="accordion-button gap-3 d-flex justify-content-between align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sopItemCollapse2" aria-expanded="false" aria-controls="sopItemCollapse2">
                        <i class="fas fa-file-contract text-primary"></i>
                        <span id="sop-item-2-preview-title">SOP 2</span>
                      </button>
                      <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div id="sopItemCollapse2" class="accordion-collapse collapse" aria-labelledby="sopItemHeading2" data-bs-parent="#sopItemsAccordion">
                      <div class="accordion-body">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Judul SOP 2</label>
                              <input type="text" name="sop_2_title" class="form-control sop-item-title-input" placeholder="Contoh: SOP Keselamatan Kerja" data-item="2">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Link Dokumen SOP</label>
                              <input type="url" name="sop_2_document_link" class="form-control" placeholder="https://drive.google.com/...">
                              <div class="form-text text-xs">Link Google Drive untuk dokumen SOP lengkap</div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-12">
                            <div class="mb-3">
                              <label class="form-label">Deskripsi SOP 2</label>
                              <textarea name="sop_2_description" class="form-control" rows="3" placeholder="Deskripsi singkat tentang SOP ini..."></textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- SOP Item 3 -->
                  <div class="accordion-item">
                    <div class="accordion-header d-flex align-items-center" id="sopItemHeading3">
                      <button class="accordion-button gap-3 d-flex justify-content-between align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sopItemCollapse3" aria-expanded="false" aria-controls="sopItemCollapse3">
                        <i class="fas fa-file-signature text-primary"></i>
                        <span id="sop-item-3-preview-title">SOP 3</span>
                      </button>
                      <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div id="sopItemCollapse3" class="accordion-collapse collapse" aria-labelledby="sopItemHeading3" data-bs-parent="#sopItemsAccordion">
                      <div class="accordion-body">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Judul SOP 3</label>
                              <input type="text" name="sop_3_title" class="form-control sop-item-title-input" placeholder="Contoh: SOP Penggunaan Peralatan" data-item="3">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label class="form-label">Link Dokumen SOP</label>
                              <input type="url" name="sop_3_document_link" class="form-control" placeholder="https://drive.google.com/...">
                              <div class="form-text text-xs">Link Google Drive untuk dokumen SOP lengkap</div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-12">
                            <div class="mb-3">
                              <label class="form-label">Deskripsi SOP 3</label>
                              <textarea name="sop_3_description" class="form-control" rows="3" placeholder="Deskripsi singkat tentang SOP ini..."></textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End SOP Items Accordion -->
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    </div>
    <!-- End SOP Management Content -->
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
    function showExistingImagePreviews(contents) {
      const imageFields = [
        'sop_header_image'
      ];

      imageFields.forEach(field => {
        const previewId = `${field.replace(/_/g, '-')}-preview`;
        if (contents[field] && contents[field].value) {
          $(`#${previewId}`).html(`
            <img src="${BASE_URL}/uploads/sop/${contents[field].value}" alt="Current ${field}" class="img-thumbnail" style="max-height: 120px;">
            <div class="form-text text-xs mt-1">Gambar saat ini</div>
          `);
        } else {
          $(`#${previewId}`).html('<div class="text-muted text-xs">Belum ada gambar</div>');
        }
      });
    }

    // Update SOP item title preview in real-time
    $(document).on('input', '.sop-item-title-input', function() {
      const itemNumber = $(this).data('item');
      const title = $(this).val() || `SOP ${itemNumber}`;
      $(`#sop-item-${itemNumber}-preview-title`).text(title);
    });

    // Read SOP Data
    function readSopData() {
      $.ajax({
        url: BASE_URL + '/sop-page/read',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            const data = res.data;

            // Header Section
            $('#form-sop input[name="sop_header_title"]').val(data.header?.title || '');
            $('#form-sop input[name="sop_header_subtitle"]').val(data.header?.subtitle || '');

            // Layanan Section
            $('#form-sop input[name="sop_layanan_location"]').val(data.layanan?.location || '');
            $('#form-sop input[name="sop_layanan_email"]').val(data.layanan?.email || '');
            $('#form-sop input[name="sop_layanan_hours"]').val(data.layanan?.hours || '');

            // SOP Items
            if (data.sop_items && data.sop_items.length >= 3) {
              $('#form-sop input[name="sop_1_title"]').val(data.sop_items[0]?.title || '');
              $('#form-sop textarea[name="sop_1_description"]').val(data.sop_items[0]?.description || '');
              $('#form-sop input[name="sop_1_document_link"]').val(data.sop_items[0]?.document_link || '');

              $('#form-sop input[name="sop_2_title"]').val(data.sop_items[1]?.title || '');
              $('#form-sop textarea[name="sop_2_description"]').val(data.sop_items[1]?.description || '');
              $('#form-sop input[name="sop_2_document_link"]').val(data.sop_items[1]?.document_link || '');

              $('#form-sop input[name="sop_3_title"]').val(data.sop_items[2]?.title || '');
              $('#form-sop textarea[name="sop_3_description"]').val(data.sop_items[2]?.description || '');
              $('#form-sop input[name="sop_3_document_link"]').val(data.sop_items[2]?.document_link || '');
            }

            // Show existing images
            if (data.header) {
              const contents = {
                sop_header_image: {
                  value: data.header?.image_path || ''
                }
              };
              showExistingImagePreviews(contents);
            }

            // Update SOP item titles preview
            for (let i = 1; i <= 3; i++) {
              const title = data.sop_items?.[i - 1]?.title || `SOP ${i}`;
              $(`#sop-item-${i}-preview-title`).text(title);
            }

          } else {
            showAlert('Gagal memuat data konten SOP', 'error');
          }
        },
        error: function(xhr, status, error) {
          let errorMessage = 'Terjadi kesalahan saat membaca data konten SOP.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'error');
        }
      });
    }

    // Save SOP Contents
    $('#btn-save-sop').on('click', function() {
      const btn = $(this);
      const originalText = btn.html();

      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

      const formData = new FormData($('#form-sop')[0]);

      $.ajax({
        url: BASE_URL + '/sop-page/update',
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
              readSopData();
            }, 1000);
          } else {
            showAlert(res.message || 'Gagal menyimpan konten SOP.', 'error');
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menyimpan konten SOP.';
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
        readSopData();
      }
    });

    // Initialize on document ready
    $(document).ready(function() {
      readSopData();

      // Handle form submit prevention
      $('#form-sop').on('submit', function(e) {
        e.preventDefault();
        $('#btn-save-sop').click();
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