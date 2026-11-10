<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Welcome</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(255,255,255,0.95);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      z-index: 9999;
      display: none;
    }
    .fade-in {
      animation: fadeIn 0.5s ease-in forwards;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <button id="goBtn" class="btn btn-success btn-sm">
      Go to Site
    </button>
  </div>

  <!-- Overlay Animation -->
  <div id="overlay" class="overlay">
    <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
    <h4 class="text-success">Loading... Please wait</h4>
  </div>

  <script>
    document.getElementById("goBtn").addEventListener("click", function() {
      let overlay = document.getElementById("overlay");
      overlay.style.display = "flex";
      overlay.classList.add("fade-in");

      // Redirect after animation (2 seconds)
      setTimeout(() => {
        window.location.href = "{{ route('admin.login') }}";
      }, 2000);
    });
  </script>
</body>
</html>
