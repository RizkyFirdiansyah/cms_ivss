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
</head>

<body class="g-sidenav-show  bg-gray-100">

  <?php include('includes/sidebar.php') ?> <!-- Sidebar -->

  <main class="main-content position-relative border-radius-lg ">

    <?php include('includes/navbar.php') ?><!-- Navbar -->

    <!-- Manajemen User -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Manajemen User</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3"> <!-- Search n ADD Button -->
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari user..." id="searchUser">
              </div>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-user" data-bs-toggle="modal" data-bs-target="#modal-add-user">
                <i class="fa fa-plus"></i> Tambah User
              </button>
            </div>
            <div class="table-responsive"> <!-- Table Users -->
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Program Studi</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                  </tr>
                </thead>
                <tbody id="userTableBody">
                  <tr>
                    <td colspan="5" class="text-center text-muted">Memuat data...</td>
                  </tr>
                </tbody>
              </table>
              <div id="pagination" class="d-flex gap-1 justify-content-center mt-3"></div>
            </div>
          </div>
        </div>
      </div>
      <!-- End Manajemen User -->

      <!-- Modal: Add User -->
      <div class="modal fade" id="modal-add-user" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
          <form id="form-add-user" class="modal-content" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title">Tambah User</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div id="add-user-alert"></div>
              <div class="mb-3">
                <label class="form-label">Nama</label>
                <input name="name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Program Studi</label>
                <select name="study_program_id" class="form-select" required>
                  <option value="1">Teknik Informatika</option>
                  <option value="2">Sistem Informasi Bisnis</option>
                  <option value="3">Rekayasa Teknologi Informasi</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                  <option value="mahasiswa">Mahasiswa</option>
                  <option value="dosen">Dosen</option>
                  <option value="kepala">Kepala Lab</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                  <option value="aktif" selected>Aktif</option>
                  <option value="nonaktif">Nonaktif</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn bg-gradient-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal: Edit User -->
      <div class="modal fade" id="modal-edit-user" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
          <form id="form-edit-user" class="modal-content">
            <input type="hidden" name="id_user" id="edit-id_user">
            <div class="modal-header">
              <h5 class="modal-title">Edit User</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div id="edit-user-alert"></div>
              <div class="mb-3">
                <label class="form-label">Role</label>
                <select id="edit-role" name="role" class="form-select" required>
                  <option value="mahasiswa">Mahasiswa</option>
                  <option value="dosen">Dosen</option>
                  <option value="kepala">Kepala Lab</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select id="edit-status" name="is_active" class="form-select">
                  <option value="aktif">Aktif</option>
                  <option value="nonaktif">Nonaktif</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn bg-gradient-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
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



  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";
    const DEFAULT_LIMIT = 5;
    let currentPage = 1;

    // Javascript Function Helpers
    // Generate initials from name
    function getInitials(name) {
      if (!name) return '';
      const parts = String(name).trim().split(/\s+/);
      if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
      return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
    }

    // Generate random color
    function getRandomColor() {
      const hue = Math.floor(Math.random() * 360);
      const sat = Math.floor(Math.random() * 30) + 65;
      const light = Math.floor(Math.random() * 20) + 40;
      return `hsl(${hue} ${sat}% ${light}%)`;
    }

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
      const modal = new bootstrap.Modal(document.getElementById('modal-alert'));
      modal.show();
      setTimeout(() => {
        modal.hide();
      }, 1000);
    }

    // Show confirm
    function showConfirm(message, callback) {
      $('#confirm-message').html(message);
      const modalEl = document.getElementById('modal-confirm');
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

    // Show edit user
    window.showEditUser = function(id_user, role, status) {
      const modalEl = document.getElementById('modal-edit-user');
      if (!modalEl) return console.error('Modal edit user tidak ditemukan.');

      // Isi form di modal
      document.getElementById('edit-id_user').value = id_user;
      document.getElementById('edit-role').value = role;
      document.getElementById('edit-status').value = status;

      // Tampilkan modal
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    };

    // Render user row
    function renderUserRow(usr) {
      const isActive = (usr.is_active === 'aktif' || usr.is_active === '1' || usr.is_active === 1 || usr.is_active === true);
      const statusIcon = isActive ? 'fa-circle-check text-success' : 'fa-circle-xmark text-danger';

      let avatarHtml = '';
      if (usr.photo && usr.photo !== '' && usr.photo !== null && !/default\.(png|jpg|jpeg)$/i.test(usr.photo)) {
        avatarHtml = `<div><img src="${BASE_URL}/uploads/${encodeURIComponent(usr.photo)}" class="avatar avatar-md me-3" alt="Foto Profil"></div>`;
      } else {
        const initials = getInitials(usr.name_user || usr.name || '');
        const bg = getRandomColor();
        avatarHtml = `<div class="avatar avatar-md me-3 d-flex justify-content-center align-items-center" style="background:${bg}; color:#fff; font-weight:600;">${initials}</div>`;
      }

      return `
          <tr>
            <td>
              <div class="d-flex px-2 py-1">
                ${avatarHtml}
                <div class="d-flex flex-column justify-content-center">
                  <h6 class="mb-0 text-xs">${usr.name_user}</h6>
                  <p class="text-xs text-secondary mb-0">${usr.email}</p>
                </div>
              </div>
            </td>
            <td class="text-center text-xs text-capitalize">${usr.role }</td>
            <td class="text-center text-lg"><i class="fa-solid ${statusIcon}"></i></td>
            <td class="text-center text-xs text-capitalize">${usr.name_ps}</td>
            <td class="text-center">
            <button class="btn btn-sm btn-secondary me-1" onclick="showEditUser(${usr.id}, '${usr.role}', '${usr.is_active}')">Edit</button>
            <button class="btn btn-sm btn-danger" onclick="deleteUser(${usr.id})">Delete</button>
            </td>
          </tr>
      `;
    }

    // Load users
    function loadUsers(page = 1, search = '', limit = DEFAULT_LIMIT) {
      const tbody = $('#userTableBody');
      const pagination = $('#pagination');
      tbody.html(`<tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>`);
      pagination.empty();

      $.ajax({
        url: BASE_URL + '/user/list',
        method: 'GET',
        dataType: 'json',
        data: {
          page: page,
          limit: limit,
          search: search
        },
        success: function(res) {
          tbody.empty();
          if (!res || !res.data || res.data.length === 0) {
            tbody.append(`<tr><td colspan="5" class="text-center text-muted">Tidak ada data user.</td></tr>`);
            return;
          }

          // Render rows
          res.data.forEach(u => tbody.append(renderUserRow(u)));

          // Pagination
          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';
          for (let i = 1; i <= totalPages; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 py-2 ${cls} mx-1" onclick="loadUsers(${i}, '${encodeURIComponent(search)}', ${limit})">${i}</button>`;
          }
          pagination.html(html);
          currentPage = page;
        },
        error: function(xhr, status, err) {
          console.error('Error loading users:', status, err);
          tbody.html(`<tr><td colspan="5" class="text-center text-danger">Gagal memuat data. Periksa konsol.</td></tr>`);
        }
      });
    }

    // Handler AJAX: CREATE USER
    $('#form-add-user').on('submit', function(e) {
      e.preventDefault();
      const form = this;
      const fd = new FormData(form);

      $.ajax({
        url: BASE_URL + '/user/create',
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            showAlert(res.message, 'success');
            $('#modal-add-user').modal('hide');
            form.reset();
            loadUsers(currentPage);
          } else {
            showAlert(res.message || 'Gagal menambah user.', 'error');
          }
        },
        error: function(xhr) {
          showAlert('Terjadi kesalahan saat menambah user: ' + (xhr.responseText || xhr.statusText), 'warning');
          console.error(xhr);
        }
      });
    });

    // Handler AJAX: UPDATE USER
    $('#form-edit-user').on('submit', function(e) {
      e.preventDefault();

      const formData = $(this).serialize();
      console.log(formData);

      if (!$('#edit-id_user').val()) {
        return showAlert('ID User tidak ditemukan.', 'warning');
      }

      $.ajax({
        url: `${BASE_URL}/user/update`,
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            showAlert(res.message, 'success');
            $('#modal-edit-user').modal('hide');
            loadUsers(currentPage);
          } else {
            showAlert(res.message, 'error');
          }
        },
        error: function(xhr) {
          showAlert(
            'Terjadi kesalahan saat update user: ' + (xhr.responseText || 'Unknown Error'),
            'warning'
          );
        }
      });
    });

    // Handler AJAX: DELETE USER
    function deleteUser(id) {
      showConfirm('Anda yakin ingin menghapus user ini?', function(confirmed) {
        if (!confirmed) return;
        $.ajax({
          url: BASE_URL + '/user/delete',
          method: 'POST',
          data: {
            id_user: id
          },
          dataType: 'json',
          success: function(res) {
            if (res.success) {
              showAlert(res.message || 'User berhasil dihapus.', 'success');
              loadUsers(currentPage);
            } else {
              showAlert(res.message || 'Gagal menghapus user.', 'error');
            }
          },
          error: function(xhr) {
            showAlert('Gagal menghapus user: ' + (xhr.responseText || xhr.statusText), 'warning');
            console.error(xhr);
          }
        });
      });
    }

    // Search
    let _searchTimeout = null;
    $('#searchUser').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        loadUsers(1, q, DEFAULT_LIMIT);
      }, 300);
    });

    // Init
    $(document).ready(function() {
      // initial load
      loadUsers(1, '', DEFAULT_LIMIT);

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