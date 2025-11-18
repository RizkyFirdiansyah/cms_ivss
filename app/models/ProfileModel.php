<?php
require_once '../app/config/Database.php';

class ProfileModel
{
    private $conn;

    public function __construct()
    {
        // Asumsi class Database tersedia dan mengembalikan koneksi PDO
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // =========================================================================
    //                            GETTER DATA (INDEX VIEW)
    // =========================================================================

    /**
     * Mengambil seluruh data profil, termasuk list dinamis, melalui satu PostgreSQL function.
     */
    public function getProfileById($id)
    {
        $query = "SELECT * FROM get_user_profile_data(:id);";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Sesuaikan dengan nama kolom function yang BARU

            $result['social_media']  = json_decode($result['social_media'] ?? '[]', true);
            $result['educations']    = json_decode($result['educations'] ?? '[]', true);
            $result['certificates']  = json_decode($result['certificates'] ?? '[]', true);
            $result['skills']        = json_decode($result['skills'] ?? '[]', true);
            $result['courses']       = json_decode($result['courses'] ?? '[]', true);
        }

        return $result;
    }

    // update
    public function updateBasicProfile($id, $data)
    {
        // Query dasar
        $query = "UPDATE users SET 
                name = :name, 
                email = :email, 
                address = :address";

        // Tambahkan update foto jika tersedia
        if (!empty($data['photo'])) {
            $query .= ", photo = :photo";
        }

        $query .= " WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);

            // Binding data dasar
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Binding foto jika ada
            if (!empty($data['photo'])) {
                $stmt->bindParam(':photo', $data['photo']);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("DB Error (updateBasicProfile): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update/Kelola media sosial (UPDATE/DELETE/INSERT).
     * Disinkronkan dengan Controller yang menggunakan data 'old' dan 'new'.
     */
    public function updateSosialMedia($user_id, $sosmed_old, $sosmed_new)
    {
        $this->conn->beginTransaction();

        try {
            // 1. Hapus semua sosmed milik user
            $this->conn->prepare("DELETE FROM user_social_media WHERE user_id = :user_id")
                ->execute([':user_id' => $user_id]);

            // 2. Gabungkan data lama + baru
            $merged = array_merge($sosmed_old, $sosmed_new);

            $insert = $this->conn->prepare("
            INSERT INTO user_social_media (user_id, social_media_id, link)
            VALUES (:user_id, :social_media_id, :link)
            ON CONFLICT (user_id, social_media_id)
            DO UPDATE SET link = EXCLUDED.link
        ");

            foreach ($merged as $item) {
                $name = trim($item['name'] ?? '');
                $link = trim($item['link'] ?? '');

                if ($name === '' || $link === '') continue;

                // Dapatkan atau buat ID sosmed
                $sosmed_id = $this->getOrCreateSocialMediaId($name);

                if (!$sosmed_id) continue;

                $insert->execute([
                    ':user_id' => $user_id,
                    ':social_media_id' => $sosmed_id,
                    ':link' => $link
                ]);
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("DB Error updateSosialMedia: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }


    // =========================================================================
    //                      UPDATE LIST DINAMIS (update_list)
    // =========================================================================

    /**
     * Fungsi utama untuk memperbarui list dinamis (Pendidikan/Sertifikasi/Keahlian/MK).
     * Menggunakan Transaction.
     * @param array $post_data Seluruh array $_POST dari Controller
     */
    public function updateDynamicLists($id_user, $list_type, $post_data)
    {
        $this->conn->beginTransaction();

        try {
            if ($list_type === 'pendidikan_sertifikat') {
                $this->updatePendidikanList($id_user, $post_data['pendidikan'] ?? []);
                $this->updateSertifikasiList($id_user, $post_data['sertifikat'] ?? []);
            } elseif ($list_type === 'keahlian_mk') {
                $this->updateKeahlianMataKuliahList(
                    $id_user,
                    $post_data['keahlian'] ?? [],
                    $post_data['mata_kuliah'] ?? []
                );
            } else {
                throw new Exception("Tipe list dinamis tidak valid.");
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("DB Error in updateDynamicLists: " . $e->getMessage());
            // Melempar Exception agar Controller bisa menampilkannya
            throw new Exception("DB Error: " . $e->getMessage());
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("App Error in updateDynamicLists: " . $e->getMessage());
            throw new Exception("App Error: " . $e->getMessage());
        }
    }

    // ------------------- LOGIKA UPDATE PER ITEM -------------------

    private function updatePendidikanList($id, $pendidikan_list)
    {
        // Hapus semua data lama user ini
        $this->conn->prepare("DELETE FROM educations WHERE user_id = :user_id")
            ->execute([':user_id' => $id]);

        if (!empty($pendidikan_list)) {

            $query = "INSERT INTO educations (user_id, level, major, institution_name, graduation_year) 
                  VALUES (:user_id, :level, :major, :institution_name, :graduation_year)";
            $stmt = $this->conn->prepare($query);

            foreach ($pendidikan_list as $item) {

                // FIX: cocokkan dengan NAME pada form
                $level   = $item['level'] ?? null;
                $major   = $item['major'] ?? null;
                $inst    = $item['institution'] ?? null;     // sesuai views
                $year    = $item['year'] ?? null;            // sesuai views

                if (empty($level)) continue; // validasi benar

                $stmt->execute([
                    ':user_id' => $id,
                    ':level' => $level,
                    ':major' => $major,
                    ':institution_name' => $inst,
                    ':graduation_year' => $year
                ]);
            }
        }

        return true;
    }


    private function updateSertifikasiList($id, $sertifikat_list)
    {
        // Hapus semua data lama user ini
        $this->conn->prepare("DELETE FROM certificates WHERE user_id = :user_id")
            ->execute([':user_id' => $id]);

        if (!empty($sertifikat_list)) {

            // Kolom di DB: user_id, name, issuer, issue_year
            $query = "INSERT INTO certificates (user_id, name, issuer, issue_year)
                  VALUES (:user_id, :name, :issuer, :issue_year)";

            $stmt = $this->conn->prepare($query);

            foreach ($sertifikat_list as $item) {

                // Sesuaikan dengan atribut input di view
                $name   = $item['name'] ?? null;
                $issuer = $item['issuer'] ?? null;
                $year   = $item['year'] ?? null;

                // Validasi minimal
                if (empty($name)) continue;

                $stmt->execute([
                    ':user_id'   => $id,
                    ':name'      => $name,
                    ':issuer'    => $issuer,
                    ':issue_year' => $year
                ]);
            }
        }

        return true;
    }


    private function updateKeahlianMataKuliahList($id_user, $keahlian_list, $mk_list)
    {

        // 1. UPDATE KEAHLIAN (FK ke tabel keahlian)
        $this->conn->prepare("DELETE FROM detail_keahlian WHERE id_user = :id_user")->execute([':id_user' => $id_user]);
        if (!empty($keahlian_list)) {
            $stmt_keahlian = $this->conn->prepare("INSERT INTO detail_keahlian (id_user, id_keahlian) VALUES (:id_user, :id_keahlian)");
            foreach ($keahlian_list as $nama_keahlian) {
                $id_keahlian = $this->getOrCreateKeahlianId($nama_keahlian);
                if ($id_keahlian) $stmt_keahlian->execute([':id_user' => $id_user, ':id_keahlian' => $id_keahlian]);
            }
        }

        // 2. UPDATE MATA KULIAH (FK ke tabel mata_kuliah)
        $stmt_dosen = $this->conn->prepare("SELECT id_dosen FROM dosen WHERE id_user = :id_user");
        $stmt_dosen->execute([':id_user' => $id_user]);
        $id_dosen = $stmt_dosen->fetchColumn();

        if ($id_dosen) {
            // Hapus semua mata kuliah yang diampu dosen ini
            $this->conn->prepare("DELETE FROM kuliah WHERE id_dosen = :id_dosen")->execute([':id_dosen' => $id_dosen]);

            if (!empty($mk_list)) {
                $stmt_mk = $this->conn->prepare("INSERT INTO kuliah (id_dosen, id_mk) VALUES (:id_dosen, :id_mk)");
                foreach ($mk_list as $nama_mk) {
                    $id_mk = $this->getOrCreateMataKuliahId($nama_mk); // Menggunakan helper yang sudah diperbaiki
                    if ($id_mk) $stmt_mk->execute([':id_dosen' => $id_dosen, ':id_mk' => $id_mk]);
                }
            }
        }
        return true;
    }

    // ------------------- FUNGSI PEMBANTU (HELPER FUNCTIONS) -------------------

    /**
     * Cek/Buat ID Keahlian baru jika belum ada.
     */
    private function getOrCreateKeahlianId($nama)
    {
        $stmt_check = $this->conn->prepare("SELECT id_keahlian FROM keahlian WHERE nama_keahlian = :nama");
        $stmt_check->bindParam(':nama', $nama);
        $stmt_check->execute();
        $id = $stmt_check->fetchColumn();

        if (!$id) {
            $stmt_insert = $this->conn->prepare("INSERT INTO keahlian (nama_keahlian) VALUES (:nama) RETURNING id_keahlian");
            $stmt_insert->bindParam(':nama', $nama);
            $stmt_insert->execute();
            $id = $stmt_insert->fetchColumn();
        }
        return $id;
    }

    /**
     * Cek/Buat ID Mata Kuliah baru jika belum ada.
     * FIX KRITIS: Menambahkan nilai default untuk kolom NOT NULL.
     */
    private function getOrCreateMataKuliahId($nama)
    {
        $stmt_check = $this->conn->prepare("SELECT id_mk FROM mata_kuliah WHERE nama_mk = :nama");
        $stmt_check->bindParam(':nama', $nama);
        $stmt_check->execute();
        $id = $stmt_check->fetchColumn();

        if (!$id) {
            // FIX: Tambahkan kolom NOT NULL yang diperlukan
            $stmt_insert = $this->conn->prepare("
                INSERT INTO mata_kuliah (nama_mk) 
                VALUES (:nama) 
                RETURNING id_mk
            ");
            $stmt_insert->execute([
                ':nama' => $nama,
            ]);
            $id = $stmt_insert->fetchColumn();
        }
        return $id;
    }

    /**
     * Cek/Buat ID Sosial Media baru jika belum ada (Tabel Master).
     */
    private function getOrCreateSocialMediaId($name)
    {
        // 1. Cek apakah sudah ada
        $stmt = $this->conn->prepare("SELECT id FROM social_media_types WHERE name = :name");
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) return $row['id'];

        // 2. Jika tidak ada → buat baru
        $insert = $this->conn->prepare("INSERT INTO social_media_types (name) VALUES (:name) RETURNING id");
        $insert->execute([':name' => $name]);
        return $insert->fetchColumn();
    }
}
// END ProfileModel.php