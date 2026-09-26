<?php
$modalStatusOptions = $modalStatusOptions ?? [];
$formTarget = $formTarget ?? 'companies-grid.php';
?>

<!-- Add Partner Modal -->
<div class="modal fade" id="addPartnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Mitra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?php echo htmlspecialchars($formTarget); ?>" enctype="multipart/form-data">
                <input type="hidden" name="form_action" value="create">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Industri / Segmen <span class="text-danger">*</span></label>
                            <input type="text" name="industri" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Mitra <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih status</option>
                                <?php foreach ($modalStatusOptions as $statusOption): ?>
                                    <option value="<?php echo htmlspecialchars($statusOption); ?>"><?php echo htmlspecialchars($statusOption); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kontak Utama</label>
                            <input type="text" name="kontak" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="telepon" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" rows="3" class="form-control" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-1">Pratinjau Logo</label>
                            <div class="partner-logo-wrapper border rounded d-flex align-items-center justify-content-center mb-2" style="height: 180px;">
                                <img src="assets/img/logo.png" alt="Pratinjau Logo" data-logo-preview="add" style="max-height: 160px; max-width: 100%; object-fit: contain;">
                            </div>
                            <label class="form-label">Unggah Logo (opsional)</label>
                            <input type="file" name="logo_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                            <span class="form-text">Format: PNG/JPG/SVG/WEBP, maksimum 4 MB.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Mitra</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Partner Modal -->
<div class="modal fade" id="editPartnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Mitra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?php echo htmlspecialchars($formTarget); ?>" enctype="multipart/form-data">
                <input type="hidden" name="form_action" value="update">
                <input type="hidden" name="partner_id" value="">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Industri / Segmen <span class="text-danger">*</span></label>
                            <input type="text" name="industri" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Mitra <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih status</option>
                                <?php foreach ($modalStatusOptions as $statusOption): ?>
                                    <option value="<?php echo htmlspecialchars($statusOption); ?>"><?php echo htmlspecialchars($statusOption); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kontak Utama</label>
                            <input type="text" name="kontak" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="telepon" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" rows="3" class="form-control" required></textarea>
                        </div>
                        <div class="col-12">
                            <input type="hidden" name="logo_path" value="">
                            <label class="form-label mb-1">Logo Saat Ini</label>
                            <div class="partner-logo-wrapper border rounded d-flex align-items-center justify-content-center mb-2" style="height: 180px;">
                                <img src="assets/img/logo.png" alt="Logo Saat Ini" data-logo-preview="edit" style="max-height: 160px; max-width: 100%; object-fit: contain;">
                            </div>
                            <span class="form-text d-block mb-2" data-current-logo>Belum ada logo tersimpan.</span>
                            <label class="form-label">Unggah Logo Baru (opsional)</label>
                            <input type="file" name="logo_file" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                            <span class="form-text">Format: PNG/JPG/SVG/WEBP, maksimum 4 MB.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
