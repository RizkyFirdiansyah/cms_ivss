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
  <link id="pagestyle" href="/mvc-pbl/public/assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" /> <!-- CSS Files -->
</head>

<body class="g-sidenav-show  bg-gray-100">

  <?php include('includes/sidebar.php') ?> <!-- Sidebar -->

  <main class="main-content position-relative border-radius-lg ">

    <?php include('includes/navbar.php') ?><!-- Navbar -->

    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Manajemen User</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari user...">
              </div>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-user" data-bs-toggle="modal" data-bs-target="#modal-add-user">
                <i class="fa fa-plus"></i> Tambah User
              </button>
            </div>
            <div class="table-responsive">
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
                <input name="nama" type="text" class="form-control" required>
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
                <select name="id_ps" class="form-select" required>
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
                <select name="status" class="form-select">
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
                <input name="password" type="password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select id="edit-status" name="status" class="form-select">
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

  <?php require_once 'includes/footer.php'; ?> <!-- Footer -->

  <!--   Core JS Files   -->
  <script src="/mvc-pbl/public/assets/js/core/popper.min.js"></script>
  <script src="/mvc-pbl/public/assets/js/core/bootstrap.min.js"></script>
  <script src="/mvc-pbl/public/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/mvc-pbl/public/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="/mvc-pbl/public/assets/js/plugins/chartjs.min.js"></script>

  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> <!-- Link Jquery -->


  <script>
    // Pastikan jQuery sudah dimuat sebelum skrip ini
    const BASE_URL = "<?= BASE_URL ?>";
    let currentPage = 1;
    const limit = 3;

    /**
     * Mengambil data user menggunakan jQuery AJAX
     * @param {number} page - Halaman saat ini.
     * @param {string} search - Keyword pencarian.
     */
    function loadUsers(page = 1, search = '') {
      // Menggunakan $.ajax()
      $.ajax({
        url: `${BASE_URL}/user/list`,
        method: 'GET',
        dataType: 'json',
        data: {
          page: page,
          limit: limit,
          search: search
        },
        success: function(res) {
          const tbody = $('#userTableBody');
          tbody.empty(); // Menggantikan innerHTML = ''
          const pagination = $('#pagination');
          pagination.empty();

          if (!res.data || res.data.length === 0) {
            tbody.append(`<tr><td colspan="5" class="text-center text-muted">Tidak ada data user.</td></tr>`);
            return;
          }

          // --- Render Data Tabel ---
          let tableRows = '';
          res.data.forEach(usr => {
            const statusIcon = usr.status === 'aktif' ? 'fa-circle-check text-success' : 'fa-circle-xmark text-danger';
            tableRows += `
                    <tr >
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div><img src="${BASE_URL}/uploads/${usr.foto ?? 'default.png'}" class="avatar avatar-sm me-3" alt=""></div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-xs">${usr.nama}</h6>
                                    <p class="text-xs text-secondary mb-0">${usr.email}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-center text-xs">${usr.role}</td>
                        <td class="text-center text-sm"><i class="fa-solid ${statusIcon}"></i></td>
                        <td class="text-center text-xs">${usr.nama_ps ?? '-'}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-secondary" onclick="showEditUser(${usr.id_user}, '${usr.role}', '${usr.status}')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(${usr.id_user})">Delete</button>
                        </td>
                    </tr>`;
          });
          tbody.append(tableRows);

          // --- Render Pagination ---
          const totalPages = Math.ceil(res.total / limit);
          let paginationHTML = '';
          for (let i = 1; i <= totalPages; i++) {
            const btnClass = i === page ? 'btn-primary' : 'btn-outline-primary';
            const searchVal = $('#searchUser').val();
            paginationHTML += `<button class="btn btn-sm px-3 py-2 ${btnClass} mx-1" onclick="loadUsers(${i}, '${searchVal}')">${i}</button>`;
          }
          pagination.append(paginationHTML);

          currentPage = page;
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('Error fetching users:', textStatus, errorThrown);
          $('#userTableBody').append(`<tr><td colspan="5" class="text-center text-danger">Gagal memuat data. Periksa konsol.</td></tr>`);
        }
      });
    }

    // --- Tambah User (Menggunakan $.ajax) ---
    $(document).ready(function() {
      $('#form-add-user').on('submit', function(e) {
        alert('test');
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
          url: `${BASE_URL}/user/create`,
          method: 'POST',
          data: formData,
          processData: false, // Penting untuk FormData
          contentType: false, // Penting untuk FormData
          dataType: 'json',
          success: function(res) {
            if (res.success) {
              alert('User berhasil ditambahkan!');
              $('#form-add-user')[0].reset();
              loadUsers();
              bootstrap.Modal.getInstance(document.getElementById('modal-add-user')).hide();
            } else {
              alert('Gagal menambah user.');
            }
          },
          error: function() {
            alert('Terjadi kesalahan saat menambah user.');
          }
        });
      });

      // --- Edit User (Data Fetching disederhanakan, hanya bagian Update yang diubah ke $.ajax) ---
      $('#form-edit-user').on('submit', function(e) {
        e.preventDefault();
        // Data diambil dari form
        const formData = $(this).serialize();

        $.ajax({
          url: `${BASE_URL}/user/update`,
          method: 'POST',
          data: formData, // Data sudah di-serialize
          dataType: 'json',
          success: function(res) {
            if (res.success) {
              alert('User berhasil diperbarui!');
              loadUsers(currentPage);
              bootstrap.Modal.getInstance(document.getElementById('modal-edit-user')).hide();
            } else {
              alert('Gagal memperbarui user.');
            }
          },
          error: function() {
            alert('Terjadi kesalahan saat memperbarui user.');
          }
        });
      });

      // --- Delete User (Menggunakan $.ajax) ---
      window.deleteUser = function(id) {
        if (!confirm("Yakin hapus user ini?")) return;

        $.ajax({
          url: `${BASE_URL}/user/delete`,
          method: 'POST',
          data: {
            id_user: id
          }, // Mengirim data sebagai objek
          dataType: 'json',
          success: function(res) {
            if (res.success) {
              alert('User berhasil dihapus!');
              loadUsers(currentPage);
            } else {
              alert('Gagal menghapus user.');
            }
          },
          error: function() {
            alert('Terjadi kesalahan saat menghapus user.');
          }
        });
      }

      // --- Inisialisasi dan Pencarian ---
      loadUsers();

      $('#searchUser').on('input', function() {
        loadUsers(1, $(this).val());
      });
    });

    // Fungsi showEditUser tetap menggunakan vanilla JS karena sudah ringkas, 
    // tetapi perlu diperbaiki untuk passing role dan status yang belum ada di loadUsers loop Anda
    // Catatan: Anda tidak mengambil role dan status di loadUsers loop, jadi perlu perbaikan di sana atau fetch data user di sini.
    window.showEditUser = function(id_user, role, status) {
      // Skenario terbaik: Fetch data user berdasarkan ID di sini
      // Skenario sederhana (mengikuti struktur Anda): Isi hanya ID, dan isian lain (role/status) diisi melalui fetch detail.
      document.getElementById('edit-id_user').value = id_user;
      document.getElementById('edit-role').value = role;
      document.getElementById('edit-status').value = status;
      new bootstrap.Modal(document.getElementById('modal-edit-user')).show();
      // **PERHATIAN: Anda perlu menambahkan logic FETCH DETAIL USER di sini
      // karena data role dan status tidak dikirimkan ke fungsi ini dari loadUsers.**
    }
  </script>

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
  <script src="/mvc-pbl/public/assets/js/argon-dashboard.min.js?v=2.1.0"></script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>