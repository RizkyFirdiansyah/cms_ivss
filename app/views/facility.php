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

    <!-- fasilitas -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Fasilitas</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3"> <!-- Search n ADD Button -->
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari fasilitas..." id="searchFacility">
              </div>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-user" data-bs-toggle="modal" data-bs-target="#modal-add-fasilitas">
                <i class="fa fa-plus"></i> Tambah Fasilitas
              </button>
            </div>
            <div class="row g-3 my-3" id="facility-container"></div>
            <div id="pagination" class="mt-4 text-center"></div>

          </div>
        </div>
        <!-- End fasilitas -->
  </main>

  <!-- Modal:fasilitas -->
  <div class="modal fade" id="modal-add-fasilitas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-add-fasilitas" class="modal-content" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Tambah fasilitas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="add-fasilitas-alert"></div>
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input name="name" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" type="text" class="form-control" cols="30" rows="5"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-control-label">Foto</label>
            <input class="form-control" type="file" name="photo">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn bg-gradient-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  <!-- End Modal:fasilitas -->

  <!-- Modal Edit fasilitas -->
  <div class="modal fade" id="modal-edit-fasilitas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <form id="form-edit-fasilitas" class="modal-content" enctype="multipart/form-data">

        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="old_photo" id="edit-old-photo">

        <div class="modal-header">
          <h5 class="modal-title">Edit Fasilitas</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div id="edit-fasilitas-alert"></div>
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input name="name" id="edit-name" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" id="edit-description" class="form-control" cols="30" rows="10"></textarea>
          </div>
          <div class="text-center">
            <img id="edit-preview" class="img-fluid rounded mb-2 ">
          </div>
          <div class=" mb-3">
            <label class="form-label">Foto Baru (opsional)</label>
            <input type="file" name="photo" class="form-control">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn bg-gradient-primary" type="submit">Simpan</button>
        </div>

      </form>
    </div>
  </div>
  <!-- End Modal Edit fasilitas -->

  <!-- Modal Detail Fasilitas -->
  <div class="modal fade" id="modal-detail-facility" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Fasilitas</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-3">
            <img id="detail-preview" class="img-fluid rounded" style="max-height: 300px;">
          </div>
          <h4 id="detail-title" class="mb-3"></h4>
          <div class="mb-3">
            <strong>Nama:</strong>
            <span id="detail-name" class="ms-2"></span>
          </div>
          <div class="mb-3">
            <strong>Deskripsi:</strong>
            <span id="detail-description" class="ms-2"></span>
          </div>
          <div class="content-box">
            <p id="detail-content" class="text-justify"></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal Detail Fasilitas -->

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
    const DEFAULT_LIMIT = 4;
    let currentPage = 1;

    // Javascript Function Helpers
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
      const modal = new bootstrap.Modal($('#modal-alert')[0]);
      modal.show();
      setTimeout(() => {
        modal.hide();
      }, 1000);
    }

    // Show confirm
    function showConfirm(message, callback) {
      $('#confirm-message').html(message);
      const modalEl = $('#modal-confirm')[0];
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

    function truncateText(text, maxLength = 80) {
      if (!text) return '';
      if (text.length <= maxLength) {
        return text;
      }
      // Potong teks dan tambahkan elipsis
      return text.substring(0, maxLength) + '...';
    }

    // Render fasilitas card
    function renderFacilityCard(f) {
      const shortDescription = truncateText(f.description, 70);
      const safeDescription = f.description ? f.description.replace(/"/g, '&quot;') : '';
      const safeName = f.name ? f.name.replace(/"/g, '&quot;') : '';

      return `
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="card border-0 shadow-xs overflow-hidden h-100 rounded-4">
            <img src="${BASE_URL}/uploads/facility/${f.photo}" class="card-img-top object-fit-cover" style="height: 160px;" alt="">
            <div class="card-body p-3">
              <h6 class="mb-1 fw-bold">${f.name}</h6>
              <p class="text-muted text-xxs mb-3">${shortDescription}</p>
              <div class="d-flex gap-2">
                <button class="btn mb-0 px-3 btn-info btn-sm text-xs detail-btn"
                    onclick="showDetailFacility(${f.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn mb-0 px-3 btn-secondary btn-sm text-xs edit-btn"
                  onclick="showEditFacility(this)"
                  data-id="${f.id}"
                  data-name="${safeName}"
                  data-description="${safeDescription}"
                  data-photo="${f.photo}">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn mb-0 px-3 btn-danger btn-sm text-xs"
                  onclick="deleteFacility(${f.id})">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        `;
    }

    // Load fasilitas
    function loadFacilities(page = 1, search = '', limit = DEFAULT_LIMIT) {
      const tbody = $('#facility-container');
      const pagination = $('#pagination');
      tbody.html(`<div class="text-center text-muted py-3">Memuat data...</div>`);
      pagination.empty();
      console.log(search);

      $.ajax({
        url: BASE_URL + '/fasilitas/list',
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
            tbody.append(`<tr><td colspan="5" class="text-center text-muted">Tidak ada data fasilitas.</td></tr>`);
            return;
          }
          // Render rows
          res.data.forEach(f => tbody.append(renderFacilityCard(f)));

          // Pagination
          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';
          for (let i = 1; i <= totalPages; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 py-2 ${cls} mx-1" onclick="loadFacilities(${i}, '${encodeURIComponent(search)}', ${limit})">${i}</button>`;
          }
          pagination.html(html);
          currentPage = page;
        },
        error: function(xhr, status, err) {
          console.error('Error loading fasilitas:', status, err);
          tbody.html(`<tr><td colspan="5" class="text-center text-danger">Gagal memuat data. Periksa konsol.</td></tr>`);
        }
      });
    }

    // Show detail fasilitas
    window.showDetailFacility = function(id) {
      $.ajax({
        url: BASE_URL + "/fasilitas/getDetail",
        method: "GET",
        data: {
          id: id
        },
        dataType: "json",
        success: function(res) {
          if (res.success) {
            const facility = res.data;
            console.log(facility);
            $('#detail-name').text(facility.name);
            $('#detail-description').text(facility.description);
            $('#detail-preview').attr('src', BASE_URL + '/uploads/facility/' + (facility.photo || 'default.jpg'));

            const modal = new bootstrap.Modal($('#modal-detail-facility')[0]);
            modal.show();
          } else {
            showAlert(res.message, "error");
          }
        }
      });
    };

    // Add fasilitas
    $("#form-add-fasilitas").on("submit", function(e) {
      e.preventDefault();
      const fd = new FormData(this);

      $.ajax({
        url: BASE_URL + "/fasilitas/create",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-add-fasilitas").modal("hide");
            loadFacilities(currentPage);
          }
        }
      });
    });

    // Show edit fasilitas
    window.showEditFacility = function(el) {
      const modalEl = $('#modal-edit-fasilitas')[0];

      const $el = $(el);
      const id = $el.data('id');
      const name = $el.data('name');
      const description = $el.data('description');
      const photo = $el.data('photo');

      $('#edit-id').val(id);
      $('#edit-name').val(name);
      $('#edit-description').val(description);
      $('#edit-old-photo').val(photo);
      $('#edit-preview').attr('src', BASE_URL + '/uploads/facility/' + photo);

      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    };

    // Update fasilitas
    $("#form-edit-fasilitas").on("submit", function(e) {
      e.preventDefault();
      const fd = new FormData(this);

      $.ajax({
        url: BASE_URL + "/fasilitas/update",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-edit-fasilitas").modal("hide");
            loadFacilities(currentPage);
          }
        }
      });
    });

    // Delete fasilitas
    function deleteFacility(id) {
      showConfirm("Yakin ingin menghapus fasilitas ini?", function(ok) {
        if (!ok) return;

        $.post(BASE_URL + "/fasilitas/delete", {
          id
        }, function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) loadFacilities(currentPage);
        }, "json");
      });
    }

    // Search
    let _searchTimeout = null;
    $('#searchFacility').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        loadFacilities(1, q, DEFAULT_LIMIT);
      }, 300);
    });

    // Init
    $(document).ready(function() {
      // initial load
      loadFacilities(1, '', DEFAULT_LIMIT);

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