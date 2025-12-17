<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>
    <?= htmlspecialchars($page_title) ?>
  </title>
  <!-- Fonts -->
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

    <!-- User Data Element (hidden) -->
    <div id="user-data" data-user-role="<?= htmlspecialchars($_SESSION['role'] ?? 'kepala') ?>" data-user-id="<?= htmlspecialchars($_SESSION['id'] ?? '') ?>" style="display: none;">
    </div>

    <!-- Dashboard -->
    <div class="container-fluid pt-2 pb-4">
      <div class="row mx-1">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="m-0">Dashboard</h5>
              </div>
            </div>
          </div>

          <div class="card-body">
            <!-- Statistics Cards Section -->
            <div class="row">
              <!-- Row 1 -->
              <div class="row mb-4" id="stats-row-1">
                <div class="col-12 text-center">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat...</span>
                  </div>
                  <p class="text-sm text-muted mt-2">Memuat statistik...</p>
                </div>
              </div>

              <!-- Row 2 -->
              <div class="row mb-4 d-none" id="stats-row-2">
                <!-- Cards akan diisi oleh JavaScript -->
              </div>
            </div>

            <!-- Dataset Terbaru Section -->
            <div class="row mb-4">
              <div class="col-lg-12 ">
                <div class="card">
                  <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                      <h6 id="dataset-title">Dataset Terbaru</h6>
                    </div>
                  </div>
                  <div class="card-body p-3">
                    <div class="table-responsive">
                      <table class="table align-items-center mb-0">
                        <thead>
                          <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Judul</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Link</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Author</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Update Terakhir</th>
                          </tr>
                        </thead>
                        <tbody id="dataset-list">
                          <tr>
                            <td colspan="4" class="text-center py-4">
                              <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Memuat...</span>
                              </div>
                              <p class="text-sm text-muted mt-1">Memuat dataset...</p>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Feedback Terbaru Section (untuk kepala) -->
            <div class="row mb-4 d-none" id="feedback-section">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                      <h6>Feedback Terbaru</h6>
                    </div>
                  </div>
                  <div class="card-body p-3">
                    <div class="table-responsive">
                      <table class="table align-items-center mb-0">
                        <thead>
                          <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Nama</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Email</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Pesan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal</th>
                          </tr>
                        </thead>
                        <tbody id="feedback-list">
                          <tr>
                            <td colspan="4" class="text-center py-4">
                              <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Memuat...</span>
                              </div>
                              <p class="text-sm text-muted mt-1">Memuat feedback...</p>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Dashboard -->
  </main>

  <?php require_once 'includes/footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

  <script>
    // Global variables
    const BASE_URL = "<?= BASE_URL ?>";

    // Get user data from HTML element
    function getUserData() {
      const userDataElement = document.getElementById('user-data');
      if (userDataElement) {
        return {
          role: userDataElement.getAttribute('data-user-role') || 'kepala',
          id: userDataElement.getAttribute('data-user-id') || null
        };
      }
      return {
        role: 'kepala',
        id: null
      };
    }

    // Format number with Indonesian locale
    function formatNumber(num) {
      return num.toLocaleString('id-ID');
    }

    // Format date
    function formatDate(dateString) {
      if (!dateString) return 'Tanpa tanggal';
      try {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
          day: '2-digit',
          month: 'short',
          year: 'numeric'
        });
      } catch (e) {
        return 'Tanpa tanggal';
      }
    }

    // Format datetime
    function formatDateTime(dateString) {
      if (!dateString) return 'Tanpa tanggal';
      try {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
      } catch (e) {
        return 'Tanpa tanggal';
      }
    }

    // Truncate text
    function truncateText(text, maxLength = 50) {
      if (!text) return '';
      if (text.length <= maxLength) return text;
      return text.substring(0, maxLength) + '...';
    }

    // Get status badge color
    function getStatusBadgeColor(status) {
      const statusColors = {
        'Berjalan': 'warning',
        'Selesai': 'success',
        'Perencanaan': 'info',
        'Dibatalkan': 'danger',
        'Aktif': 'success',
        'Nonaktif': 'secondary',
        'Draft': 'secondary',
        'Published': 'success'
      };
      return statusColors[status] || 'secondary';
    }

    // Load dashboard data
    function loadDashboardData() {
      const userData = getUserData();

      $.ajax({
        url: BASE_URL + '/dashboard/getDashboardData',
        method: 'GET',
        dataType: 'json',
        beforeSend: function() {
          // Show loading state for statistics
          const row1 = document.getElementById('stats-row-1');
          if (row1) {
            row1.innerHTML = `
              <div class="col-12 text-center">
                <div class="col-12 text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</div
              </div>
            `;
          }

          // Show loading for dataset table
          const datasetTbody = document.getElementById('dataset-list');
          if (datasetTbody) {
            datasetTbody.innerHTML = `
              <tr>
                <td colspan="4" class="text-center py-4">
                  <div class="col-12 text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</div
                </td>
              </tr>
            `;
          }

        },
        success: function(response) {
          console.log('Dashboard response:', response);

          if (response.success) {
            updateDashboardUI(response);
          }
        },
        error: function(xhr, status, err) {
          console.error('Error loading dashboard:', xhr.responseText || err);
          showAlert('Terjadi kesalahan saat memuat dashboard. Silakan refresh halaman.', 'error');
        },
      });
    }

    // Update dashboard UI
    function updateDashboardUI(data) {
      console.log('Updating dashboard with data:', data);

      // Update statistics cards based on role
      updateStatsCards(data.totals || {}, data.role || 'kepala');

      // Update dataset table
      if (data.recent_datasets) {
        updateDatasetTable(data.recent_datasets);
        // updateDatasetCount(data.recent_datasets.length);
      }

      // Update feedback table only for kepala role
      if (data.role === 'kepala') {
        const feedbackSection = document.getElementById('feedback-section');
        if (feedbackSection) {
          feedbackSection.classList.remove('d-none');
        }

        if (data.recent_feedback) {
          updateFeedbackTable(data.recent_feedback);
          // updateFeedbackCount(data.recent_feedback.length);
        }
      } else {
        // Hide feedback section for other roles
        const feedbackSection = document.getElementById('feedback-section');
        if (feedbackSection) {
          feedbackSection.classList.add('d-none');
        }
      }
    }

    // Update statistics cards
    function updateStatsCards(totals, role) {
      const row1 = document.getElementById('stats-row-1');
      const row2 = document.getElementById('stats-row-2');

      if (!row1) return;

      // Clear rows
      row1.innerHTML = '';
      if (row2) row2.innerHTML = '';

      // Card template
      const cardTemplate = (title, value, iconClass, bgColor, id, description = '') => `
        <div class="col-xl-3 col-sm-6">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">${title}</p>
                    <h5 class="font-weight-bolder">
                      <span id="${id}">${formatNumber(value)}</span>
                    </h5>
                    ${description ? `<p class="text-xs text-muted mb-0">${description}</p>` : ''}
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape ${bgColor} text-center rounded-circle">
                    <i class="${iconClass} text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;

      // Card configurations based on role
      const cards = [];

      if (role === 'kepala') {
        // Kepala melihat semua statistik global
        cards.push({
          title: 'Total Member',
          value: totals.total_users || 0,
          icon: 'fa fa-users',
          bg: 'bg-gradient-primary shadow-primary',
          id: 'total-users',
          description: 'Jumlah semua pengguna'
        }, {
          title: 'Total Publikasi',
          value: totals.total_publications || 0,
          icon: 'fa fa-lines-leaning',
          bg: 'bg-gradient-success shadow-success',
          id: 'total-publications',
          description: 'Jumlah publikasi'
        }, {
          title: 'Total Berita',
          value: totals.total_news || 0,
          icon: 'fa fa-newspaper',
          bg: 'bg-gradient-info shadow-info',
          id: 'total-news',
          description: 'Jumlah berita'
        }, {
          title: 'Total Dataset',
          value: totals.total_datasets || 0,
          icon: 'fa fa-database',
          bg: 'bg-gradient-warning shadow-warning',
          id: 'total-datasets',
          description: 'Jumlah dataset'
        }, {
          title: 'Penelitian',
          value: totals.total_research || 0,
          icon: 'fa fa-flask',
          bg: 'bg-gradient-danger shadow-danger',
          id: 'total-research',
          description: 'Jumlah penelitian'
        }, {
          title: 'Galeri',
          value: totals.total_gallery || 0,
          icon: 'fa fa-images',
          bg: 'bg-gradient-dark shadow-dark',
          id: 'total-gallery',
          description: 'Jumlah galeri'
        }, {
          title: 'Feedback',
          value: totals.total_feedback || 0,
          icon: 'fa fa-comments',
          bg: 'bg-gradient-secondary shadow-secondary',
          id: 'total-feedback',
          description: 'Jumlah feedback'
        }, {
          title: 'Fasilitas',
          value: totals.total_facilities || 0,
          icon: 'fa-solid fa-toolbox',
          bg: 'bg-gradient-primary shadow-primary',
          id: 'total-facilities',
          description: 'Jumlah fasilitas'
        });

      } else if (role === 'dosen') {
        // Dosen melihat data miliknya sendiri
        cards.push({
          title: 'Publikasi Saya',
          value: totals.my_publications || 0,
          icon: 'fa fa-file-lines',
          bg: 'bg-gradient-success shadow-success',
          id: 'my-publications',
          description: 'Publikasi yang dibuat'
        }, {
          title: 'Berita Saya',
          value: totals.my_news || 0,
          icon: 'fa fa-newspaper',
          bg: 'bg-gradient-info shadow-info',
          id: 'my-news',
          description: 'Berita yang dibuat'
        }, {
          title: 'Dataset Saya',
          value: totals.my_datasets || 0,
          icon: 'fa fa-database',
          bg: 'bg-gradient-warning shadow-warning',
          id: 'my-datasets',
          description: 'Dataset yang dibuat'
        }, {
          title: 'Penelitian Saya',
          value: totals.my_research || 0,
          icon: 'fa fa-flask',
          bg: 'bg-gradient-danger shadow-danger',
          id: 'my-research',
          description: 'Penelitian yang dibuat'
        });

        // Add gallery if exists
        if (totals.my_gallery !== undefined) {
          cards.push({
            title: 'Galeri Saya',
            value: totals.my_gallery || 0,
            icon: 'fa fa-images',
            bg: 'bg-gradient-dark shadow-dark',
            id: 'my-gallery',
            description: 'Galeri yang dibuat'
          });
        }

      } else if (role === 'mahasiswa') {
        // Mahasiswa melihat data yang relevan
        cards.push({
          title: 'Berita Saya',
          value: totals.my_news || 0,
          icon: 'fa fa-newspaper',
          bg: 'bg-gradient-info shadow-info',
          id: 'my-news',
          description: 'Berita yang dibuat'
        }, {
          title: 'Dataset Saya',
          value: totals.my_datasets || 0,
          icon: 'fa fa-database',
          bg: 'bg-gradient-warning shadow-warning',
          id: 'my-datasets',
          description: 'Dataset yang dibuat'
        }, {
          title: 'Research Diikuti',
          value: totals.research_joined || 0,
          icon: 'fa fa-user-group',
          bg: 'bg-gradient-primary shadow-primary',
          id: 'research-joined',
          description: 'Research yang diikuti'
        });
      }

      // Split cards into rows (max 4 per row)
      const firstRowCards = cards.slice(0, 4);
      const secondRowCards = cards.slice(4);

      // Add cards to first row
      firstRowCards.forEach(card => {
        row1.innerHTML += cardTemplate(
          card.title,
          card.value,
          card.icon,
          card.bg,
          card.id,
          card.description
        );
      });

      // Add cards to second row if exists
      if (secondRowCards.length > 0) {
        row2.classList.remove('d-none');
        secondRowCards.forEach(card => {
          row2.innerHTML += cardTemplate(
            card.title,
            card.value,
            card.icon,
            card.bg,
            card.id,
            card.description
          );
        });
      } else {
        row2.classList.add('d-none');
      }
    }

    // Update dataset table
    function updateDatasetTable(datasetList) {
      const tbody = document.getElementById('dataset-list');
      if (!tbody) return;

      if (!Array.isArray(datasetList) || datasetList.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="4" class="text-center py-4">
              <div class="empty-state">
                <i class="fas fa-database"></i>
                <p class="text-sm text-muted mt-2">Belum ada dataset</p>
              </div>
            </td>
          </tr>
        `;
        return;
      }

      let html = '';
      datasetList.forEach((dataset) => {
        if (!dataset) return;

        // Format data
        const title = truncateText(dataset.title || '', 40);
        const link = dataset.link ? truncateText(dataset.link, 30) : '#';
        const author = dataset.author || dataset.creator_name || 'Anonim';
        const formattedDate = formatDate(dataset.updated_at || dataset.created_at);

        html += `
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div>
                  <h6 class="mb-0 text-sm" title="${dataset.title || ''}">${title}</h6>
                </div>
              </div>
            </td>
            <td class="align-middle text-center">
              ${dataset.link ? `
                <a href="${dataset.link}" target="_blank" class="btn btn-sm bg-gradient-warning text-white px-3">
                  <i class="fa fa-external-link me-1"></i>Link
                </a>
              ` : '<span class="text-xs text-muted">Tidak ada link</span>'}
            </td>
            <td class="align-middle text-center">
              <p class="text-xs font-weight-bold mb-0">${author}</p>
            </td>
            <td class="align-middle text-center">
              <span class="text-xs font-weight-bold">${formattedDate}</span>
            </td>
          </tr>
        `;
      });

      tbody.innerHTML = html;
    }

    // Update feedback table
    function updateFeedbackTable(feedbackList) {
      const tbody = document.getElementById('feedback-list');
      if (!tbody) return;

      if (!Array.isArray(feedbackList) || feedbackList.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="4" class="text-center py-4">
              <div class="empty-state">
                <i class="fas fa-comments"></i>
                <p class="text-sm text-muted mt-2">Belum ada feedback</p>
              </div>
            </td>
          </tr>
        `;
        return;
      }

      let html = '';
      feedbackList.forEach((feedback) => {
        if (!feedback) return;

        // Format data
        const name = feedback.name || 'Tidak ada nama';
        const email = feedback.email || 'tidak ada email';
        const message = truncateText(feedback.content || feedback.message || '', 50);
        const formattedDate = formatDate(feedback.created_at);

        html += `
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div>
                  <h6 class="mb-0 text-sm">${name}</h6>
                </div>
              </div>
            </td>
            <td class="align-middle text-center">
              <p class="text-xs font-weight-bold mb-0">${email}</p>
            </td>
            <td class="align-middle text-center">
              <p class="text-xs text-secondary mb-0" title="${feedback.content || feedback.message || ''}">${message}</p>
            </td>
            <td class="align-middle text-center">
              <span class="text-xs font-weight-bold">${formattedDate}</span>
            </td>
          </tr>
        `;
      });

      tbody.innerHTML = html;
    }

    // Initialize dashboard
    $(document).ready(function() {
      // Initial load
      loadDashboardData();

      // Auto-refresh every 5 minutes
      setInterval(loadDashboardData, 300000);
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