    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="logout-modal-overlay">
        <div class="logout-modal-card">
            <div class="logout-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3>Xác nhận đăng xuất</h3>
            <p>Bạn có chắc chắn muốn rời khỏi hệ thống Farmi Admin không?</p>
            <div class="logout-actions">
                <button type="button" class="btn-modal btn-cancel" onclick="hideLogoutModal()">Hủy</button>
                <button type="button" class="btn-modal btn-confirm-logout" onclick="confirmLogout()">Đăng xuất</button>
            </div>
        </div>
    </div>

    <script>
        const logoutModal = document.getElementById('logoutModal');
        
        function showLogoutModal(event) {
            if (event) event.preventDefault();
            logoutModal.classList.add('active');
        }

        function hideLogoutModal() {
            logoutModal.classList.remove('active');
        }

        function confirmLogout() {
            window.location.href = '/hi/public/index.php?url=logout';
        }

        // Close modal when clicking outside the card
        window.onclick = function(event) {
            if (event.target == logoutModal) {
                hideLogoutModal();
            }
        }
    </script>
</body>
</html>