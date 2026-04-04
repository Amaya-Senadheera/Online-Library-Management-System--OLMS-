<?php include('../includes/header.php'); ?>

<style>
.hover-link:hover {
    text-decoration: underline !important;
}

.card-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    background-color: #FDFBF7 !important; /* 🟢 Vintage Cream 🟢 */
    border: 1px solid rgba(176, 138, 91, 0.3) !important; /* Subtle Ochre border */
}

.card-hover:hover {
    transform: translateY(-8px);
    /* 🟢 Warmer, vintage shadow instead of stark black 🟢 */
    box-shadow: 0 15px 30px rgba(28, 17, 10, 0.15) !important; 
}
</style>

<div class="container mt-4 mb-5">

    <div class="mb-4">
        <a href="../index.php" class="text-decoration-none fw-bold hover-link" style="color: #8C3A35;">
            <i class="bi bi-arrow-left me-2"></i>Back to Homepage
        </a>
    </div>

    <div class="p-5 mb-5 rounded-4 shadow-lg text-white text-center" 
         style="background-image: linear-gradient(rgba(18, 17, 17, 0.79), rgba(0, 0, 0, 0.64)), url('../assets/images/hero-bg.jpg'); background-size: cover; background-position: center;">
        <h1 class="display-5 fw-bold mb-3">Admin Dashboard</h1>
        <p class="lead mb-0 text-white-50">
            Manage your library system efficiently. Add, update, and monitor books easily.
        </p>
    </div>

    <div class="row g-4 mt-2">

        <div class="col-md-4">
            <div class="card card-hover shadow-sm border-0 text-center p-4 rounded-4 h-100">
                <h4 class="fw-bold mb-3" style="color: #1C110A;">
                    <i class="bi bi-plus-circle me-2" style="color: #82a841;"></i>Add Book
                </h4>
                <p class="text-muted mb-4">Add new books to the library collection.</p>
                <a href="add_books.php" class="btn fw-bold w-100 mt-auto text-white rounded-pill shadow-sm" style="background-color: #82a841; border: none;">Go to Add Book</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-hover shadow-sm border-0 text-center p-4 rounded-4 h-100">
                <h4 class="fw-bold mb-3" style="color: #1C110A;">
                    <i class="bi bi-journals me-2" style="color: #8C3A35;"></i>Manage Books
                </h4>
                <p class="text-muted mb-4">Edit details or remove books from the system.</p>
                <a href="manage_books.php" class="btn fw-bold w-100 mt-auto text-white rounded-pill shadow-sm" style="background-color: #8C3A35; border: none;">Go to Manage</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-4 rounded-4 h-100 opacity-75" style="background-color: #E9D9B2;">
                <h4 class="fw-bold mb-3" style="color: #1C110A;">
                    <i class="bi bi-graph-up me-2" style="color: #B08A5B;"></i>Reports
                </h4>
                <p class="text-muted mb-4">View library activity and borrowing statistics.</p>
                <button class="btn fw-bold w-100 mt-auto text-white rounded-pill" style="background-color: #B08A5B; border: none;" disabled>Coming Soon</button>
            </div>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>