<?php

namespace BackendApi\Models;

class MediaModel {
    private $db;
    private $uploadDir;

    public function __construct($db) {
        $this->db = $db;
        $this->uploadDir = dirname(__DIR__) . '/public/uploads/';
    }

    public function getAllMedia() {
        $query = "SELECT m.*, u.username as uploaded_by
                 FROM media m
                 LEFT JOIN users u ON m.user_id = u.id
                 ORDER BY m.created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    public function getMedia($id) {
        $query = "SELECT m.*, u.username as uploaded_by
                 FROM media m
                 LEFT JOIN users u ON m.user_id = u.id
                 WHERE m.id = ?";

        return $this->db->query($query, [$id])->fetch();
    }

    public function create($data, $file) {
        $this->db->beginTransaction();

        try {
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new \Exception('Failed to upload file');
            }

            // Insert media record
            $query = "INSERT INTO media (user_id, filename, original_name, mime_type, size, type, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, NOW())";

            $this->db->query($query, [
                $data['user_id'],
                $filename,
                $file['name'],
                $file['type'],
                $file['size'],
                $data['type'] ?? 'image'
            ]);

            $mediaId = $this->db->lastInsertId();

            $this->db->commit();
            return $mediaId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            if (isset($filepath) && file_exists($filepath)) {
                unlink($filepath);
            }
            throw $e;
        }
    }

    public function update($id, $data) {
        $query = "UPDATE media
                 SET title = ?, description = ?, type = ?, updated_at = NOW()
                 WHERE id = ?";

        return $this->db->query($query, [
            $data['title'] ?? null,
            $data['description'] ?? null,
            $data['type'] ?? 'image',
            $id
        ]);
    }

    public function delete($id) {
        $this->db->beginTransaction();

        try {
            // Get media info
            $media = $this->getMedia($id);
            if (!$media) {
                throw new \Exception('Media not found');
            }

            // Delete file
            $filepath = $this->uploadDir . $media['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            // Delete record
            $query = "DELETE FROM media WHERE id = ?";
            $this->db->query($query, [$id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getUserMedia($userId, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM media
                 WHERE user_id = ?
                 ORDER BY created_at DESC
                 LIMIT ? OFFSET ?";

        return $this->db->query($query, [$userId, $limit, $offset])->fetchAll();
    }

    public function getContestMedia($contestId, $limit = 10, $offset = 0) {
        $query = "SELECT m.*, u.username as uploaded_by
                 FROM media m
                 LEFT JOIN users u ON m.user_id = u.id
                 WHERE m.type = 'contest' AND m.target_id = ?
                 ORDER BY m.created_at DESC
                 LIMIT ? OFFSET ?";

        return $this->db->query($query, [$contestId, $limit, $offset])->fetchAll();
    }

    public function getContestantMedia($contestantId, $limit = 10, $offset = 0) {
        $query = "SELECT m.*, u.username as uploaded_by
                 FROM media m
                 LEFT JOIN users u ON m.user_id = u.id
                 WHERE m.type = 'contestant' AND m.target_id = ?
                 ORDER BY m.created_at DESC
                 LIMIT ? OFFSET ?";

        return $this->db->query($query, [$contestantId, $limit, $offset])->fetchAll();
    }

    public function getMediaStats($userId = null) {
        $query = "SELECT
                    COUNT(*) as total_media,
                    COUNT(CASE WHEN type = 'image' THEN 1 END) as total_images,
                    COUNT(CASE WHEN type = 'video' THEN 1 END) as total_videos,
                    COUNT(CASE WHEN type = 'document' THEN 1 END) as total_documents,
                    SUM(size) as total_size,
                    MIN(created_at) as first_upload,
                    MAX(created_at) as last_upload";

        if ($userId) {
            $query .= " FROM media WHERE user_id = ?";
            return $this->db->query($query, [$userId])->fetch();
        } else {
            $query .= " FROM media";
            return $this->db->query($query)->fetch();
        }
    }

    public function validateFile($file) {
        $errors = [];

        // Check file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            $errors[] = 'File size exceeds 10MB limit';
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'application/pdf'];
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Invalid file type';
        }

        return $errors;
    }

    public function getFileUrl($filename) {
        return '/uploads/' . $filename;
    }

    public function getThumbnailUrl($filename) {
        $pathinfo = pathinfo($filename);
        return '/uploads/thumbnails/' . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
    }
}