<!--Start Footer-->            
    <footer class="footer text-center text-sm-start d-print-none">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-0 rounded-bottom-0">
                        <div class="card-body">
                            <p class="text-muted mb-0">
                                &copy; 2026 K&R Legal. All Rights Reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<!--end footer-->

<!-- ============================================================== -->
<!-- GLOBAL DELETE CONFIRMATION MODAL -->
<!-- ============================================================== -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog my-centered-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this item? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>


<!-- ============================================================== -->
<!-- GLOBAL JAVASCRIPT FOR THE DELETE MODAL -->
<!-- ============================================================== -->