<?php

    session_start();

    include_once '../bd/CAD.php';

    $cad = new CAD();
    $user = null;

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $user = $cad->getUserById($_SESSION['user_id']);
    }

    if ($user === null || $user['role'] !== 'admin') {
        header('Location: ../index.php');
        exit;
    }

    $isNewsletterSubscriber = $user ? $cad->getUserWithNewsletter($user['id']) : false;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Post - BYTE Y PIXEL</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../editormd/css/editormd.css" />
</head>
<body>
    <div class="container">
        <div class="topnav" id="myTopnav">
            <a href="../index.php" class="active">BYTE Y PIXEL</a>
            <div class="navoptions" id="navOptions">
            <a href="about.php">About</a>
                <?php if ($user) { ?>
                    <?php if ($isNewsletterSubscriber) { ?>
                    <a href="newsletter.php">Newsletter</a>
                <?php } ?>
                    <?php if ($user['role'] === 'admin') { ?>
                    <a href="admin.php">Admin</a>
                <?php } ?>
                <a href="../bd/logout.php">Logout</a>
                <?php } else { ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                <?php } ?>
            </div>
        </div>

        <main class="main">
            <h1 class="welcome">Hello Admin!</h1>

            <nav class="admin-nav">
                <a href="admin.php">MANAGE POSTS</a>
                <a href="admin2.php">BLOG SUBSCRIBERS</a>
                <a href="admin3.php">UPLOAD POST</a>
            </nav>

            <section>
                <h2 class="section-title" >Upload post</h2>
                <form id="uploadForm" class="upload-form" enctype="multipart/form-data">
                    <input type="text" class="form-input" placeholder="Title post" required name="title">
                    <input type="number" class="form-input" placeholder="Read time" required name="readTime">
                    <input type="text" class="form-input" placeholder="Description" required name="description">

                    <div id="test-editor">
                        <textarea style="display:none;" name="content">### Start writing your post here</textarea>
                    </div>

                    <p class="upload-area-label">Upload Principal Image</p>
                    <div class="upload-area" id="dropZone">
                        <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                        <p class="upload-text">Drag & drop files or <span class="browse">Browse</span></p>
                        <p class="upload-formats">Supported formats: JPEG, PNG, GIF</p>
                    </div>
                    <input type="file" name="image" id="file-input" accept="image/*" required>
                    <div class="preview-area" id="previewArea" style="display: none;">
                        <img class="preview-image" id="previewImage">
                        <p class="file-info" id="fileInfo"></p>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-container">
                            <input type="checkbox" name="is_newsletter_exclusive" id="is_newsletter_exclusive" checked />
                            <span class="checkbox-label">Newsletter Exclusive Content</span>
                        </label>
                        <p class="help-text">Check this if the post should only be visible to newsletter subscribers</p>
                    </div>
                    <button type="submit" class="save-button">Save post</button>
                </form>
            </section>
        </main>
    </div>

    <footer>
        <div class="footer-categories">
            DIGITAL PRODUCT DESIGN • REMOTE WORK • UX DESIGN • DISTRIBUTED TEAMS
        </div>
        <div class="footer-logo">BYTE Y PIXEL</div>
        <div class="footer-copyright">© 2024 BYTE Y PIXEL All rights reserved.</div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="../editormd/editormd.min.js"></script>

    <script type="text/javascript">
        let testEditor;

        $(function() {
            testEditor = editormd("test-editor", {
                path: "../editormd/lib/",
                height: 640,
                saveHTMLToTextarea: true
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('file-input');
            const previewArea = document.getElementById('previewArea');
            const previewImage = document.getElementById('previewImage');
            const fileInfo = document.getElementById('fileInfo');
            const browseButton = document.querySelector('.browse');
            const uploadForm = document.getElementById('uploadForm');

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
                handleFiles(e.dataTransfer.files);
            });

            browseButton.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });

            function handleFiles(files) {
                const file = files[0];
                if (!file.type.startsWith('image/')) {
                    alert('Please upload only image files.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    fileInfo.textContent = `Name: ${file.name} - Size: ${formatFileSize(file.size)}`;
                    previewArea.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('content', testEditor.getHTML());

                const alertDiv = document.createElement('div');
                alertDiv.style.marginBottom = '20px';

                const newsletterCheckbox = document.getElementById('is_newsletter_exclusive');
                formData.set('is_newsletter_exclusive', newsletterCheckbox.checked ? '1' : '0');

                fetch('../bd/post.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    alertDiv.className = 'alert alert-success';
                    alertDiv.textContent = 'Post created successfully!';
                    uploadForm.insertBefore(alertDiv, uploadForm.firstChild);

                    // redirect to form section
                    window.location.href = '#uploadForm';


                })
                .catch(error => {
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.textContent = 'Error uploading post: ' + error.message;
                    uploadForm.insertBefore(alertDiv, uploadForm.firstChild);

                    // redirect to top page

                });
            });
        });

        function myFunction() {
            var x = document.getElementById("myTopnav");
            if (x.className === "topnav") {
                x.className += " responsive";
            } else {
                x.className = "topnav";
            }
        }
    </script>
</body>
</html>
