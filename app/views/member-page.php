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

    <!-- Member Management Content -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Manajemen Halaman Member</h5>
                <div>
                  <button class="btn btn-sm btn-success mb-0" id="btn-save-member">
                    <i class="fas fa-save me-1"></i> Simpan
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body p-0">
              <form id="form-member" enctype="multipart/form-data">
                <!-- Input hidden untuk file lama -->
                <input type="hidden" name="old_member_header_image" id="old_member_header_image" value="">

                <!-- Header Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-users me-2"></i>Header Section</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Judul Header</label>
                          <input type="text" name="member_header_title" class="form-control" placeholder="Tim Kami">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Subjudul Header</label>
                          <input type="text" name="member_header_subtitle" class="form-control" placeholder="Kenali anggota laboratorium kami">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="form-label">Gambar Background Header</label>
                          <input type="file" name="member_header_image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                          <div class="form-text text-xs">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</div>
                          <div class="mt-2" id="member-header-image-preview">
                            <!-- Header image preview akan diisi via JavaScript -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Members Preview Section -->
                <div class="card m-4">
                  <h6 class="me-2 mb-0 pb-0 py-4 mx-4 text-primary"><i class="fas fa-user-friends me-2"></i>Preview Member</h6>
                  <div class="card-body">
                    <div class="row">
                      <div class="alert alert-warning text-white">
                        <i class="fas fa-info-circle me-2"></i>
                        Data member diambil secara otomatis dari <strong>User Management</strong> (hanya user aktif).
                        <a href="<?= BASE_URL ?>/users" class="alert-link">Kelola user di sini</a>.
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="mb-3">
                          <button type="button" class="btn btn-sm btn-outline-primary" id="btn-preview-members">
                            <i class="fas fa-eye me-1"></i> Preview Daftar Member
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
    <!-- End Member Management Content -->
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

  <!-- Members Preview Modal -->
  <div class="modal fade" id="membersModal" tabindex="-1" aria-labelledby="membersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="membersModalLabel">
            <i class="fas fa-user-friends me-2"></i>Preview Member
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row" id="members-preview-container">
            <!-- Members preview akan diisi via JavaScript -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Members Preview Modal -->

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
        'member_header_image'
      ];

      imageFields.forEach(field => {
        const previewId = `${field.replace(/_/g, '-')}-preview`;
        if (contents[field] && contents[field].value) {
          $(`#${previewId}`).html(`
            <img src="${BASE_URL}/uploads/member/${contents[field].value}" alt="Current ${field}" class="img-thumbnail" style="max-height: 120px;">
            <div class="form-text text-xs mt-1">Gambar saat ini</div>
          `);
        } else {
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
        readMemberData();
      }
    });

    // Load members preview
    function loadMembersPreview() {
      const modalElement = document.getElementById('membersModal');
      const myModal = bootstrap.Modal.getOrCreateInstance(modalElement);

      $.ajax({
        url: BASE_URL + '/member/active-members',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          const container = $('#members-preview-container');
          container.empty();

          if (res.success && res.data && res.data.length > 0) {
            // Group members by role
            const groupedMembers = groupMembersByRole(res.data);

            // Create accordion
            let accordionHTML = `
          <div class="accordion" id="membersAccordion">
        `;

            // Define role order and display names
            const roleOrder = [{
                key: 'kepala_lab',
                display: 'Kepala Laboratorium',
                icon: 'fa-crown',
                badge: 'bg-danger'
              },
              {
                key: 'dosen',
                display: 'Dosen',
                icon: 'fa-user-tie',
                badge: 'bg-success'
              },
              {
                key: 'plp',
                display: 'PLP (Pranata Laboratorium Pendidikan)',
                icon: 'fa-flask',
                badge: 'bg-info'
              },
              {
                key: 'mahasiswa',
                display: 'Mahasiswa',
                icon: 'fa-graduation-cap',
                badge: 'bg-warning'
              }
            ];

            // Generate accordion items for each role group
            roleOrder.forEach((roleInfo, index) => {
              const members = groupedMembers[roleInfo.key] || [];
              if (members.length === 0) return;

              const accordionId = `accordion-${roleInfo.key}`;
              const collapseId = `collapse-${roleInfo.key}`;
              const isFirst = index === 0;

              accordionHTML += `
            <div class="accordion-item border-0 mb-3">
              <h2 class="accordion-header" id="${accordionId}">
                <button class="accordion-button ${isFirst ? '' : 'collapsed'} bg-light shadow-none" 
                        type="button" data-bs-toggle="collapse" 
                        data-bs-target="#${collapseId}" 
                        aria-expanded="${isFirst ? 'true' : 'false'}" 
                        aria-controls="${collapseId}">
                  <div class="d-flex align-items-center w-100">
                    <i class="fas ${roleInfo.icon} text-${roleInfo.badge.split('-')[1]} me-2"></i>
                    <span class="fw-bold me-2">${roleInfo.display}</span>
                    <span class="badge ${roleInfo.badge} ms-auto">${members.length} orang</span>
                  </div>
                </button>
              </h2>
              <div id="${collapseId}" class="accordion-collapse collapse ${isFirst ? 'show' : ''}" 
                   aria-labelledby="${accordionId}" data-bs-parent="#membersAccordion">
                <div class="accordion-body p-3">
                  <div class="row">
          `;

              // Add members for this role
              members.forEach((member) => {
                const roleBadge = getRoleBadge(member.role);
                accordionHTML += `
              <div class="col-xl-4 col-lg-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-3">
                    ${member.photo ? `
                      <img src="${BASE_URL}/uploads/profile/${member.photo}" 
                           class="rounded-circle mb-2 border" 
                           style="width: 80px; height: 80px; object-fit: cover;" 
                           alt="${member.name}">
                    ` : `
                      <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2 border" 
                           style="width: 80px; height: 80px;">
                        <i class="fas fa-user text-muted"></i>
                      </div>
                    `}
                    <h6 class="card-title mb-1 fw-bold text-dark">${member.name}</h6>
                    <div class="mb-2">${roleBadge}</div>
                  </div>
                </div>
              </div>
            `;
              });
              accordionHTML += `
                  </div>
                </div>
              </div>
            </div>
          `;
            });

            accordionHTML += `</div>`; // Close accordion

            container.html(accordionHTML);

          } else {
            container.html(`
          <div class="col-12">
            <div class="text-center text-muted py-5">
              <i class="fas fa-users fa-3x mb-3"></i>
              <h5 class="mb-2">Belum ada member aktif</h5>
              <p class="text-sm mb-0">Aktifkan user melalui menu <a href="${BASE_URL}/admin/users" class="text-primary">User Management</a></p>
            </div>
          </div>
        `);
          }

          // Tampilkan modal setelah konten dimuat
          myModal.show();
        },
        error: function(xhr) {
          const container = $('#members-preview-container');
          container.html(`
        <div class="col-12">
          <div class="text-center text-danger py-5">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h5 class="mb-2">Gagal memuat data member</h5>
            <p class="text-sm mb-0">Terjadi kesalahan saat mengambil data</p>
          </div>
        </div>
      `);

          const modalElement = document.getElementById('membersModal');
          const myModal = bootstrap.Modal.getOrCreateInstance(modalElement);
          myModal.show();
        }
      });
    }

    // Helper function to group members by role
    function groupMembersByRole(members) {
      const groups = {
        kepala_lab: [],
        dosen: [],
        plp: [],
        mahasiswa: []
      };

      members.forEach(member => {
        const role = member.role?.toLowerCase() || '';

        if (role.includes('kepala') || role.includes('head') || role.includes('leader')) {
          groups.kepala_lab.push(member);
        } else if (role.includes('dosen') || role.includes('lecturer')) {
          groups.dosen.push(member);
        } else if (role.includes('plp') || role.includes('laboran')) {
          groups.plp.push(member);
        } else if (role.includes('mahasiswa') || role.includes('student')) {
          groups.mahasiswa.push(member);
        } else {
          confole.log('Unknown role:', role);
        }
      });

      return groups;
    }

    // Get role badge dengan styling yang lebih baik
    function getRoleBadge(role) {
      const badges = {
        'kepala_lab': 'badge bg-danger',
        'dosen': 'badge bg-success',
        'plp': 'badge bg-info',
        'mahasiswa': 'badge bg-warning'
      };

      const roleText = {
        'kepala_lab': 'Kepala Lab',
        'dosen': 'Dosen',
        'plp': 'PLP',
        'mahasiswa': 'Mahasiswa'
      };

      const roleKey = role?.toLowerCase() || '';
      const badgeClass = badges[roleKey] || 'badge bg-dark';
      const roleDisplay = roleText[roleKey] || role;

      return `<span class="${badgeClass}">${roleDisplay}</span>`;
    }

    // Preview Members
    $('#btn-preview-members').off('click').on('click', function() {
      loadMembersPreview();
    });

    // Read Member Data
    function readMemberData() {
      $.ajax({
        url: BASE_URL + '/member/read',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            // Header Section
            $('#form-member input[name="member_header_title"]').val(res.data.header?.title || '');
            $('#form-member input[name="member_header_subtitle"]').val(res.data.header?.subtitle || '');

            // Show existing images
            if (res.data.header) {
              const contents = {
                member_header_image: {
                  value: res.data.header?.image_path || ''
                }
              };
              showExistingImagePreviews(contents);
            }

          } else {
            showAlert('Gagal memuat data konten member', 'error');
          }
        },
        error: function(xhr, status, error) {
          let errorMessage = 'Terjadi kesalahan saat membaca data konten member.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          showAlert(errorMessage, 'error');
        }
      });
    }

    // Save Member Contents
    $('#btn-save-member').on('click', function() {
      const btn = $(this);
      const originalText = btn.html();

      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

      const formData = new FormData($('#form-member')[0]);

      $.ajax({
        url: BASE_URL + '/member/update',
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
              readMemberData();
            }, 1000);
          } else {
            showAlert(res.message || 'Gagal menyimpan konten member.', 'error');
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menyimpan konten member.';
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
      readMemberData();

      // Handle form submit prevention
      $('#form-member').on('submit', function(e) {
        e.preventDefault();
        $('#btn-save-member').click();
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