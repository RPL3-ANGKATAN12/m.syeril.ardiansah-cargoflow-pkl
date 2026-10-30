        </main>
    </div>

    <!-- Mobile Sidebar Drawer Backdrop Script -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const appSidebar = document.getElementById('appSidebar');
        if (menuToggle && appSidebar) {
            menuToggle.addEventListener('click', () => {
                appSidebar.classList.toggle('-translate-x-full');
            });
        }
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>
