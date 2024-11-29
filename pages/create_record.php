<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /webdev/pages/login.php');
    exit;
}
include('../includes/db.php');
include('../includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $discovery_date = $_POST['discovery_date'];
    $category = $_POST['category'];
    $image_url = trim($_POST['image_url']);
    
    // Validate image URL
    $isValidImage = false;
    if (!empty($image_url)) {
        // Check if URL ends with an image extension
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $path_info = pathinfo(parse_url($image_url, PHP_URL_PATH));
        
        if (isset($path_info['extension']) && in_array(strtolower($path_info['extension']), $valid_extensions)) {
            $isValidImage = true;
        } else if (strpos($image_url, 'data:image') === 0) {
            $isValidImage = true;
        }
    }
    
    if (!empty($image_url) && !$isValidImage) {
        $error = "Please enter a valid image URL or base64 image";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO space_discoveries (user_id, title, description, discovery_date, category, image_url) 
                                  VALUES (:user_id, :title, :description, :discovery_date, :category, :image_url)");
            
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'title' => $title,
                'description' => $description,
                'discovery_date' => $discovery_date,
                'category' => $category,
                'image_url' => $image_url ?: null
            ]);
            
            header('Location: /webdev/pages/view_records.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error saving record. Please try again.";
        }
    }
}
?>

<main>
    <div class="form-container">
        <h2>Add New Space Discovery</h2>
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="post" id="discoveryForm" class="discovery-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="discovery_date">Discovery Date</label>
                <input type="date" id="discovery_date" name="discovery_date" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="Planet">Planet</option>
                    <option value="Star">Star</option>
                    <option value="Galaxy">Galaxy</option>
                    <option value="Nebula">Nebula</option>
                    <option value="Black Hole">Black Hole</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="url" id="image_url" name="image_url" 
                       placeholder="https://example.com/image.jpg">
                <small class="form-help">Enter a direct link to an image (jpg, png, gif)</small>
            </div>
            
            <button type="submit">Add Discovery</button>
        </form>
    </div>

    <script>
    document.getElementById('discoveryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const category = document.getElementById('category').value;
        const discoveryDate = document.getElementById('discovery_date').value;
        
        // Title validation
        if (title.length < 3 || title.length > 100) {
            showError('title', 'Title must be between 3 and 100 characters');
            isValid = false;
        }
        
        // Description validation
        if (description.length < 10) {
            showError('description', 'Description must be at least 10 characters');
            isValid = false;
        }
        
        // Category validation
        if (!category) {
            showError('category', 'Please select a category');
            isValid = false;
        }
        
        // Date validation
        if (!discoveryDate) {
            showError('discovery_date', 'Please select a discovery date');
            isValid = false;
        }
        
        if (isValid) {
            this.submit();
        }
    });

    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
    </script>
</main>

<?php include('../includes/footer.php'); ?>
