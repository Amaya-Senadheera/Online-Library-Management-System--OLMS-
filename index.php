<?php
include 'includes/header.php';

$books_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM books");
$total_books = mysqli_fetch_assoc($books_query)['total'];

$members_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'member'");
$active_members = mysqli_fetch_assoc($members_query)['total'];

$borrowed_query = mysqli_query($conn, "SELECT SUM(total_qty - available_qty) as total FROM books");
$borrowed_books = mysqli_fetch_assoc($borrowed_query)['total'];
$borrowed_books = $borrowed_books ? $borrowed_books : 0;

// UPDATED: Fetch the 5 latest books, and store them in an array
$latest_books_query = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT 5");
$latest_books = [];
while ($row = mysqli_fetch_assoc($latest_books_query)) {
    $latest_books[] = $row;
}
?>

<style>
    /* 1. Updated Carousel Wrapper */
    .carousel-wrapper {
        position: relative;
        padding: 0 45px;
        max-width: 1050px;
        /* Shrinks track width to fit exactly 4 books */
        margin: 0 auto;
        /* Centers the track perfectly on the screen */
    }

    /* The Scrollable Track */
    .scroll-container {
        display: flex;
        gap: 1.5rem;
        overflow-x: auto;
        scroll-behavior: smooth;
        /* Smooth gliding when arrows are clicked */
        padding: 15px 5px;
        scrollbar-width: none;
        /* Hides scrollbar in Firefox */
    }

    /* Hides scrollbar in Chrome/Safari/Edge */
    .scroll-container::-webkit-scrollbar {
        display: none;
    }

    /* 🟢 Navigation Arrows (Updated to Vintage Red-Brown) 🟢 */
    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #8C3A35; /* Vintage Red-Brown */
        color: white;
        border: none;
        box-shadow: 0 4px 8px rgba(28, 17, 10, 0.3);
        transition: all 0.2s;
    }

    .carousel-btn:hover {
        background-color: #6E2D29; /* Darker Red-Brown */
        transform: translateY(-50%) scale(1.1);
    }

    .btn-left {
        left: 0;
    }

    .btn-right {
        right: 0;
    }

    .book-card-custom {
        width: 220px;
        min-width: 220px;
        /* Forces card to NEVER squish */
        max-width: 220px;
        /* Forces card to NEVER stretch */
        flex: 0 0 auto;
        /* Locks the card size to be perfectly constant */
        white-space: normal;
    }

    .book-cover-wrapper {
        position: relative;
        overflow: hidden;
    }

    .book-cover-custom {
        height: 320px;
        width: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .book-card-custom:hover .book-cover-custom {
        transform: scale(1.05);
    }

    /* 🟢 Badges (Updated to Vintage Olive and Red-Brown) 🟢 */
    .badge-available {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #82a841; /* Vintage Olive */
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-unavailable {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #8C3A35; /* Vintage Red-Brown */
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

/* Refined Dark Cinematic Hero */
    .hero-faded {
        position: relative;
        background-color: #1C110A; /* Dark fallback to match theme */
        /* Smooth dark gradient fading from left to right */
        background-image: 
            linear-gradient(to right, rgba(28, 17, 10, 0.7) 0%, rgba(28, 17, 10, 0.4) 100%), 
            url('assets/images/hero-bg.jpg'); 
        background-size: cover;
        background-position: center;
        border: none;
    }

    /* Clean, subtle dark shadow to make white text pop */
    .text-shadow-dark {
        text-shadow: 0 4px 12px rgba(0,0,0,0.6);
    }

</style>

<div class="p-4 mb-5 rounded-4 shadow-sm text-center hero-faded">
    <div class="py-4 position-relative" style="z-index: 2;">
        <h1 class="display-4 fw-bold text-white text-shadow-dark" style="letter-spacing: -1px;">Welcome to OLMS</h1>
        <p class="lead mb-4 fs-5 text-light text-shadow-dark">Your Modern Gateway to Knowledge. Discover, manage, and read seamlessly.</p>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="catalog/books.php" class="btn btn-primary btn-lg px-5 shadow border-0">
                 Start Browsing
            </a>
        </div>
    </div>
</div>

<div class="row text-center mb-5">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 py-4">
            <h2 class="display-5 fw-bold" style="color: #8C3A35;"><?php echo $total_books; ?></h2>
            <p class="text-muted mb-0 text-uppercase fw-semibold">Total Books</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 py-4">
            <h2 class="display-5 fw-bold" style="color: #757A45;"><?php echo $active_members; ?></h2>
            <p class="text-muted mb-0 text-uppercase fw-semibold">Active Members</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 py-4">
            <h2 class="display-5 fw-bold" style="color: #c4b106;"><?php echo $borrowed_books; ?></h2>
            <p class="text-muted mb-0 text-uppercase fw-semibold">Books Borrowed</p>
        </div>
    </div>
</div>

<div class="mb-5">
    <h3 class="mb-4 text-center fw-bold text-dark">New Arrivals</h3>

    <div class="carousel-wrapper">
        <button id="btnScrollLeft" class="carousel-btn btn-left shadow-sm">❮</button>

        <div class="scroll-container" id="bookCarousel">

            <?php
            // Loop just once through the 5 latest books
            foreach ($latest_books as $book):
                ?>
                <div class="card h-100 shadow-sm border-0 hover-card book-card-custom">
                    <div class="book-cover-wrapper">
                        <?php
                        $cover = $book['cover_image'];
                        if (filter_var($cover, FILTER_VALIDATE_URL)) {
                            $image_path = $cover;
                        } else {
                            $image_path = $base_url . "assets/images/" . rawurlencode($cover);
                        }
                        ?>
                        <img src="<?php echo $image_path; ?>"
                            class="card-img-top book-cover-custom"
                            alt="<?php echo htmlspecialchars($book['title']); ?> Cover"
                            onerror="this.onerror=null; this.src='<?php echo $base_url; ?>assets/images/default-cover.jpg';">

                        <?php if ($book['available_qty'] > 0): ?>
                            <span class="badge-available shadow-sm">Available</span>
                        <?php else: ?>
                            <span class="badge-unavailable shadow-sm">Borrowed</span>
                        <?php endif; ?>
                    </div>

                    <div class="card-body d-flex flex-column text-center mt-2 p-3">
                        <h6 class="card-title fw-bold mb-1 text-truncate"
                            title="<?php echo htmlspecialchars($book['title']); ?>" style="font-size: 1rem;">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </h6>
                        <p class="card-text text-muted mb-2" style="font-size: 0.85rem;">
                            <?php echo htmlspecialchars($book['author']); ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                            <span class="badge bg-info text-dark"
                                style="font-size: 0.7rem;"><?php echo htmlspecialchars($book['category']); ?></span>
                            <small class="fw-bold" style="font-size: 0.75rem; color: #757A45;">
                                <?php echo $book['available_qty']; ?>/<?php echo $book['total_qty']; ?> left
                            </small>
                        </div>

                        <div class="mt-auto">
                            <a href="<?php echo $base_url; ?>catalog/book_details.php?id=<?php echo $book['id']; ?>"
                                class="btn btn-outline-primary btn-sm w-100 fw-bold">View Details</a>
                        </div>
                    </div>
                </div>
                <?php
            endforeach;
            ?>

        </div>

        <button id="btnScrollRight" class="carousel-btn btn-right shadow-sm">❯</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const carousel = document.getElementById('bookCarousel');
        const btnLeft = document.getElementById('btnScrollLeft');
        const btnRight = document.getElementById('btnScrollRight');

        // Width of card (220px) + gap (1.5rem = ~24px)
        const scrollAmount = 244;

        btnRight.addEventListener('click', function () {
            carousel.scrollLeft += scrollAmount;
        });

        btnLeft.addEventListener('click', function () {
            carousel.scrollLeft -= scrollAmount;
        });
    });
</script>

<?php include 'includes/footer.php'; ?>