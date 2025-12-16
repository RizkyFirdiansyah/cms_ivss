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

    <!-- Feedback -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Manajemen Feedback</h5>
            <p class="text-sm mb-0">Kelola feedback dari pengunjung web profile</p>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari feedback..." id="searchFeedback">
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pengirim</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Isi Feedback</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                  </tr>
                </thead>
                <tbody id="feedbackTableBody">
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
    </div>
    <!-- End Feedback -->
  </main>

  <!-- Modal: Detail Feedback -->
  <div class="modal fade" id="modal-detail-feedback" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Feedback</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Nama Pengirim</label>
            <p id="detail-name" class="form-control-static"></p>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Email</label>
            <p id="detail-email" class="form-control-static"></p>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Isi Feedback</label>
            <div id="detail-content" class="p-3 bg-gray-100 rounded" style="min-height: 100px;"></div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Tanggal Kirim</label>
            <p id="detail-created-at" class="form-control-static"></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Alert & Konfirmasi -->
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

  <?php require_once 'includes/footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";
    const DEFAULT_LIMIT = 5;
    let currentPage = 1;
    let currentSearch = '';

    // Helper functions
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
      }, 3000);
    }

    function showConfirm(message, callback) {
      $('#confirm-message').html(message);
      const modalEl = $('#modal-confirm')[0];
      const modal = new bootstrap.Modal(modalEl);
      $('#confirm-yes').off('click').on('click', function() {
        modal.hide();
        if (typeof callback === 'function') callback(true);
      });
      modal.show();
    }

    // Show detail feedback 
    window.showDetailFeedback = function(id) {
      $.ajax({
        url: BASE_URL + '/feedback/detail?id=' + id,
        method: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res.success && res.data) {
            const feedback = res.data;
            $('#detail-name').text(feedback.name || '-');
            $('#detail-email').text(feedback.email || '-');
            $('#detail-content').text(feedback.content || '-');
            $('#detail-created-at').text(new Date(feedback.created_at).toLocaleString('id-ID'));

            new bootstrap.Modal('#modal-detail-feedback').show();
          } else {
            showAlert('Gagal memuat detail feedback', 'error');
          }
        },
        error: function(xhr) {
          showAlert('Gagal memuat detail feedback', 'error');
        }
      });
    };

    // Delete feedback 
    window.deleteFeedback = function(id) {
      showConfirm("Yakin ingin menghapus feedback ini?", function(ok) {
        if (!ok) return;

        $.ajax({
          url: BASE_URL + "/feedback/delete",
          method: "POST",
          data: {
            id: id
          },
          dataType: "json",
          success: function(res) {
            showAlert(res.message, res.success ? "success" : "error");
            if (res.success) loadFeedback(currentPage, currentSearch);
          },
          error: function(xhr, status, error) {
            showAlert('Terjadi kesalahan saat menghapus feedback', 'error');
          }
        });
      });
    };

    // Render feedback row
    function renderFeedbackRow(feedback) {
      const contentPreview = feedback.content.length > 100 ?
        feedback.content.substring(0, 100) + '...' :
        feedback.content;

      const createdDate = new Date(feedback.created_at).toLocaleDateString('id-ID');
      const createdTime = new Date(feedback.created_at).toLocaleTimeString('id-ID');

      return `
    <tr>
      <td>
        <div class="d-flex flex-column justify-content-center">
          <h6 class="mb-0 text-sm">${feedback.name}</h6>
          <small class="text-muted">Guest</small>
        </div>
      </td>
      <td class="text-sm align-middle">
        <a href="mailto:${feedback.email}" class="text-primary">${feedback.email}</a>
      </td>
      <td class="align-middle">
        <p class="text-sm mb-0" title="${feedback.content.replace(/"/g, '&quot;')}">${contentPreview}</p>
      </td>
      <td class="text-center align-middle">
        <span class="text-secondary text-xs font-weight-bold">${createdDate}</span><br>
        <span class="text-secondary text-xs">${createdTime}</span>
      </td>
      <td class="text-center align-middle">
        <button class="btn btn-xs btn-info" onclick="showDetailFeedback(${feedback.id})">
          <i class="fa fa-eye 1"></i>
        </button>
        <button class="btn btn-xs btn-danger" onclick="deleteFeedback(${feedback.id})">
          <i class="fa fa-trash 1"></i>
        </button>
      </td>
    </tr>
  `;
    }

    // Load feedback
    function loadFeedback(page = 1, search = '', limit = DEFAULT_LIMIT) {
      const tbody = $('#feedbackTableBody');
      const pagination = $('#pagination');
      tbody.html(`<tr><td colspan="5" class="text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>`);
      pagination.empty();

      $.ajax({
        url: BASE_URL + '/feedback/list',
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
            let message = 'Tidak ada data feedback.';
            if (search) message += ` untuk pencarian "${search}"`;
            tbody.append(`<tr><td colspan="5" class="text-center text-muted">${message}</td></tr>`);
            return;
          }

          res.data.forEach(f => tbody.append(renderFeedbackRow(f)));

          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';

          // Previous button
          if (page > 1) {
            html += `<button class="btn btn-sm btn-outline-primary mx-1" onclick="loadFeedback(${page - 1}, '${encodeURIComponent(search)}', ${limit})">
                  <i class="fas fa-chevron-left"></i>
                </button>`;
          }

          // Page numbers
          const startPage = Math.max(1, page - 2);
          const endPage = Math.min(totalPages, startPage + 4);

          for (let i = startPage; i <= endPage; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 ${cls} mx-1" onclick="loadFeedback(${i}, '${encodeURIComponent(search)}', ${limit})">${i}</button>`;
          }

          // Next button
          if (page < totalPages) {
            html += `<button class="btn btn-sm btn-outline-primary mx-1" onclick="loadFeedback(${page + 1}, '${encodeURIComponent(search)}', ${limit})">
                  <i class="fas fa-chevron-right"></i>
                </button>`;
          }

          pagination.html(html);
          currentPage = page;
          currentSearch = search;
        },
        error: function(xhr, status, err) {
          tbody.html(`<tr><td colspan="5" class="text-center text-danger">Gagal memuat data.</td></tr>`);
        }
      });
    }

    // Search dengan debounce
    let _searchTimeout = null;
    $('#searchFeedback').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        currentSearch = q;
        loadFeedback(1, q, DEFAULT_LIMIT);
      }, 300);
    });

    // Init 
    $(document).ready(function() {
      // initial load feedback
      loadFeedback(1, '', DEFAULT_LIMIT);
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