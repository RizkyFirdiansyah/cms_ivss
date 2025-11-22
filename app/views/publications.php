<?php
$page_title = "Publikasi";
$page_breadcrumb = ["Pages", "Publikasi"];
?>
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

    <!-- Publications -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <h5 class="m-0">Manajemen Publikasi</h5>
          </div>

          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between gap-3">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Cari publikasi..." id="searchPublication">
              </div>
              <button class="btn btn-sm btn-success m-0 p-0 w-25" id="btn-add-publication" data-bs-toggle="modal" data-bs-target="#modal-add-publication">
                <i class="fa fa-plus"></i> Tambah Publikasi
              </button>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul Publikasi</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tahun</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Link/File</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Terakhir Diupdate</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                  </tr>
                </thead>
                <tbody id="publicationTableBody">
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
    <!-- End Publications -->
  </main>

<!-- Modal: Tambah Publikasi -->
<div class="modal fade" id="modal-add-publication" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <form id="form-add-publication" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Publikasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="add-publication-alert"></div>
        <div class="mb-3">
          <label class="form-label">Judul Publikasi</label>
          <input name="title" type="text" class="form-control" required>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Tahun Publikasi</label>
              <input name="publication_year" type="number" class="form-control" min="1900" max="2030" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Link Publikasi</label>
              <input name="link" type="url" class="form-control" placeholder="https://example.com" required>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn bg-gradient-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Edit Publikasi -->
<div class="modal fade" id="modal-edit-publication" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <form id="form-edit-publication" class="modal-content" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header">
        <h5 class="modal-title">Edit Publikasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="edit-publication-alert"></div>
        <div class="mb-3">
          <label class="form-label">Judul Publikasi</label>
          <input name="title" id="edit-title" type="text" class="form-control" required>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Tahun Publikasi</label>
              <input name="publication_year" id="edit-publication_year" type="number" class="form-control" min="1900" max="2030" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Link Publikasi</label>
              <input name="link" id="edit-link" type="url" class="form-control" placeholder="https://example.com" required>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn bg-gradient-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
  <!-- Modal Alert & Konfirmasi (sama seperti di users.php) -->
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

    // Helper functions (sama seperti di users.php)
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


    // Render publication row
    function renderPublicationRow(pub) {
      const isUrl = pub.link && (pub.link.startsWith('http') || pub.link.includes('://'));
      const linkDisplay = isUrl ? 
        `<a href="${pub.link}" target="_blank" class=" bg-gradient-info text-white px-3 py-1 rounded text-sm">Link</a>` :
        `<span class="text-success">File Terlampir</span>`;
      
      const lastUpdated = new Date(pub.last_updated).toLocaleDateString('id-ID');

      return `
        <tr>
          <td>
            <div class="d-flex flex-column justify-content-center">
              <h6 class="mb-0 text-sm">${pub.title}</h6>
            </div>
          </td>
          <td class="text-center text-sm">${pub.publication_year}</td>
        <td class="text-center text-sm"><a href="${pub.link}" target="_blank" class=" bg-gradient-info text-white px-3 py-1 rounded text-sm">Link</a></td>
          <td class="text-center text-sm">${lastUpdated}</td>
          <td class="text-center">
          
            <button class="btn btn-sm btn-secondary me-1" onclick="showEditPublication(this)"
              data-id="${pub.id}"
                  data-title="${pub.title}"
                  data-link="${pub.link}"
                  data-year="${pub.publication_year}"
            >Edit</button>
            <button class="btn btn-sm btn-danger" onclick="deletePublication(${pub.id})">Delete</button>
          </td>
        </tr>
      `;
    }

    // Load publications
    function loadPublications(page = 1, search = '', limit = DEFAULT_LIMIT) {
      const tbody = $('#publicationTableBody');
      const pagination = $('#pagination');
      tbody.html(`<tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>`);
      pagination.empty();

      $.ajax({
        url: BASE_URL + '/publikasi/list',
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
            tbody.append(`<tr><td colspan="5" class="text-center text-muted">Tidak ada data publikasi.</td></tr>`);
            return;
          }

          res.data.forEach(p => tbody.append(renderPublicationRow(p)));

          const total = Number(res.total || 0);
          const totalPages = Math.max(1, Math.ceil(total / limit));
          let html = '';
          for (let i = 1; i <= totalPages; i++) {
            const cls = (i === page) ? 'btn-primary' : 'btn-outline-primary';
            html += `<button class="btn btn-sm px-3 py-2 ${cls} mx-1" onclick="loadPublications(${i}, '${encodeURIComponent(search)}', ${limit})">${i}</button>`;
          }
          pagination.html(html);
          currentPage = page;
        },
        error: function(xhr, status, err) {
          console.error('Error loading publications:', status, err);
          tbody.html(`<tr><td colspan="5" class="text-center text-danger">Gagal memuat data.</td></tr>`);
        }
      });
    }

    // Show edit publication - tanpa AJAX get
window.showEditPublication = function(el) {
  const $el = $(el);
  console.log('Edit button clicked:', $el);
  
  const id = $el.data('id');
  const title = $el.data('title');
  const publication_year = $el.data('year');
  const link = $el.data('link');

  console.log('Editing publication item:', { id, title, publication_year, link });

  $('#edit-id').val(id);
  $('#edit-title').val(title);
  $('#edit-publication_year').val(publication_year);
  $('#edit-link').val(link);
  
  // Reset file input
  $('#form-edit-publication input[type="file"]').val('');

  const modal = new bootstrap.Modal($('#modal-edit-publication')[0]);
  modal.show();
};

    // Add publication
    $("#form-add-publication").on("submit", function(e) {
      e.preventDefault();
      const fd = new FormData(this);
      
      $.ajax({
        url: BASE_URL + "/publikasi/create",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-add-publication").modal("hide");
            $("#form-add-publication")[0].reset();
            loadPublications(currentPage);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error adding publication:', error);
          showAlert('Terjadi kesalahan saat menambah publikasi', 'error');
        }
      });
    });

    // Update publication
    $("#form-edit-publication").on("submit", function(e) {
      e.preventDefault();
      const fd = new FormData(this);
      
      // Jika memilih URL, hapus file input
      if ($('#link-type-edit').val() === 'url') {
        fd.delete('link');
      }

      $.ajax({
        url: BASE_URL + "/publikasi/update",
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(res) {
          showAlert(res.message, res.success ? "success" : "error");
          if (res.success) {
            $("#modal-edit-publication").modal("hide");
            loadPublications(currentPage);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error updating publication:', error);
          showAlert('Terjadi kesalahan saat memperbarui publikasi', 'error');
        }
      });
    });

    // Delete publication
    function deletePublication(id) {
      showConfirm("Yakin ingin menghapus publikasi ini?", function(ok) {
        if (!ok) return;

        $.ajax({
          url: BASE_URL + "/publikasi/delete",
          method: "POST",
          data: { id: id },
          dataType: "json",
          success: function(res) {
            showAlert(res.message, res.success ? "success" : "error");
            if (res.success) loadPublications(currentPage);
          },
          error: function(xhr, status, error) {
            console.error('Error deleting publication:', error);
            showAlert('Terjadi kesalahan saat menghapus publikasi', 'error');
          }
        });
      });
    }

    // Search
    let _searchTimeout = null;
    $('#searchPublication').on('keyup', function() {
      clearTimeout(_searchTimeout);
      const q = $(this).val();
      _searchTimeout = setTimeout(() => {
        loadPublications(1, q, DEFAULT_LIMIT);
      }, 300);
    });

    // Init
    $(document).ready(function() {
      loadPublications(1, '', DEFAULT_LIMIT);
      
      // safety: ensure modals exist
      if (!document.getElementById('modal-alert')) {
        console.warn('Element #modal-alert tidak ditemukan. showAlert() membutuhkan modal ini.');
      }
      if (!document.getElementById('modal-confirm')) {
        console.warn('Element #modal-confirm tidak ditemukan. showConfirm() membutuhkan modal ini.');
      }
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