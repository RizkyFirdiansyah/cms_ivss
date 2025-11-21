<?php
require_once '../app/config/Database.php';

class ProfileModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Read Data Profile
    public function getProfileById($id)
    {
        $query = "SELECT * FROM get_user_profile_data(:id);";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $result['social_media']  = json_decode($result['social_media'] ?? '[]', true);
            $result['educations']    = json_decode($result['educations'] ?? '[]', true);
            $result['certificates']  = json_decode($result['certificates'] ?? '[]', true);
            $result['skills']        = json_decode($result['skills'] ?? '[]', true);
            $result['courses']       = json_decode($result['courses'] ?? '[]', true);
        }

        return $result;
    }

    // Update Data Profile
    public function updateBasicProfile($id, $data)
    {
        // Cek foto lama hanya jika foto baru di-upload
        $old_photo_path = null;
        if (!empty($data['photo'])) {
            $stmt_old = $this->conn->prepare("SELECT photo FROM users WHERE id = :id"); // Get path foto lama
            $stmt_old->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_old->execute();
            $old_photo_path = $stmt_old->fetchColumn();
        }

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
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Binding foto jika ada
            if (!empty($data['photo'])) {
                $stmt->bindParam(':photo', $data['photo']);
            }

            $success = $stmt->execute();

            if ($success && !empty($data['photo'])) {
                return $old_photo_path;
            }

            return $success;
        } catch (PDOException $e) {
            error_log("DB Error (updateBasicProfile): " . $e->getMessage());
            return false;
        }
    }

    // Update Data Sosmed
    public function updateSosialMedia($user_id, $sosmed_old, $sosmed_new)
    {
        $this->conn->beginTransaction();

        try {
            $this->conn->prepare("DELETE FROM user_social_media WHERE user_id = :user_id")
                ->execute([':user_id' => $user_id]);

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

    // Update Data List Dinamis
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
            throw new Exception("DB Error: " . $e->getMessage());
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("App Error in updateDynamicLists: " . $e->getMessage());
            throw new Exception("App Error: " . $e->getMessage());
        }
    }


    // Update Data Pendidikan
    private function updatePendidikanList($id_user, $pendidikan_list)
    {
        $this->conn->prepare("DELETE FROM educations WHERE user_id = :user_id")
            ->execute([':user_id' => $id_user]);

        if (!empty($pendidikan_list)) {
            $query = "INSERT INTO educations (user_id, level, major, institution_name, graduation_year) VALUES (:user_id, :level, :major, :institution_name, :graduation_year)";
            $stmt = $this->conn->prepare($query);

            foreach ($pendidikan_list as $item) {
                $level   = $item['level'] ?? null;
                $major   = $item['major'] ?? null;
                $inst    = $item['institution_name'] ?? null;
                $year    = $item['graduation_year'] ?? null;

                if (empty($level)) continue;

                $stmt->execute([
                    ':user_id' => $id_user,
                    ':level' => $level,
                    ':major' => $major,
                    ':institution_name' => $inst,
                    ':graduation_year' => $year
                ]);
            }
        }
        return true;
    }

    // Update Data Sertifikasi
    private function updateSertifikasiList($id_user, $sertifikat_list)
    {
        $this->conn->prepare("DELETE FROM certificates WHERE user_id = :user_id")
            ->execute([':user_id' => $id_user]);

        if (!empty($sertifikat_list)) {
            $query = "INSERT INTO certificates (user_id, name, issuer, issue_year) VALUES (:user_id, :name, :issuer, :issue_year)";
            $stmt = $this->conn->prepare($query);

            foreach ($sertifikat_list as $item) {
                $name   = $item['name'] ?? null;
                $issuer = $item['issuer'] ?? null;
                $year   = $item['issue_year'] ?? null;

                if (empty($name)) continue;

                $stmt->execute([
                    ':user_id'    => $id_user,
                    ':name'       => $name,
                    ':issuer'     => $issuer,
                    ':issue_year' => $year
                ]);
            }
        }
        return true;
    }

    // Update Data Keahlian dan Mata Kuliah
    private function updateKeahlianMataKuliahList($id, $keahlian_list, $mk_list)
    {
        $this->conn->prepare("DELETE FROM user_skills WHERE user_id = :user_id")->execute([':user_id' => $id]);

        if (!empty($keahlian_list)) {
            $stmt_keahlian = $this->conn->prepare("INSERT INTO user_skills (user_id, skill_id) VALUES (:user_id, :skill_id)");
            foreach ($keahlian_list as $nama_keahlian) {
                $id_keahlian = $this->getOrCreateKeahlianId($nama_keahlian);
                if ($id_keahlian) $stmt_keahlian->execute([':user_id' => $id, ':skill_id' => $id_keahlian]);
            }
        }

        $stmt_dosen = $this->conn->prepare("SELECT id FROM dosen WHERE user_id = :user_id");
        $stmt_dosen->execute([':user_id' => $id]);
        $id_dosen = $stmt_dosen->fetchColumn();

        if ($id_dosen) {
            $this->conn->prepare("DELETE FROM user_courses WHERE dosen_id = :dosen_id")->execute([':dosen_id' => $id_dosen]);

            if (!empty($mk_list)) {
                $stmt_mk = $this->conn->prepare("INSERT INTO user_courses (dosen_id, course_id) VALUES (:dosen_id, :course_id)");
                foreach ($mk_list as $nama_mk) {
                    $id_mk = $this->getOrCreateMataKuliahId($nama_mk);
                    if ($id_mk) $stmt_mk->execute([':dosen_id' => $id_dosen, ':course_id' => $id_mk]);
                }
            }
        }
        return true;
    }

    // Helper Functions
    // Cek/Buat ID Keahlian baru jika belum ada.
    private function getOrCreateKeahlianId($nama)
    {
        $stmt_check = $this->conn->prepare("SELECT id FROM skills WHERE name = :nama");
        $stmt_check->bindParam(':nama', $nama);
        $stmt_check->execute();
        $id = $stmt_check->fetchColumn();

        if (!$id) {
            $stmt_insert = $this->conn->prepare("INSERT INTO skills (name) VALUES (:nama) RETURNING id");
            $stmt_insert->bindParam(':nama', $nama);
            $stmt_insert->execute();
            $id = $stmt_insert->fetchColumn();
        }
        return $id;
    }


    // Cek/Buat ID Mata kuliah baru jika belum ada.
    private function getOrCreateMataKuliahId($nama)
    {
        $stmt_check = $this->conn->prepare("SELECT id FROM courses WHERE name = :nama");
        $stmt_check->bindParam(':nama', $nama);
        $stmt_check->execute();
        $id = $stmt_check->fetchColumn();

        if (!$id) {
            $stmt_insert = $this->conn->prepare("INSERT INTO courses (name) VALUES (:nama) RETURNING id");
            $stmt_insert->bindParam(':nama', $nama);
            $stmt_insert->execute();
            $id = $stmt_insert->fetchColumn();
        }
        return $id;
    }

    // Cek/Buat ID Media Sosial baru jika belum ada.
    private function getOrCreateSocialMediaId($name)
    {
        $stmt = $this->conn->prepare("SELECT id FROM social_media_types WHERE name = :name");
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) return $row['id'];

        $insert = $this->conn->prepare("INSERT INTO social_media_types (name) VALUES (:name) RETURNING id");
        $insert->execute([':name' => $name]);
        return $insert->fetchColumn();
    }
}
