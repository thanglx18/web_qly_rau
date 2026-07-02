<div id="sidebar-wrapper">
    <div class="sidebar-heading">FARMI ADMIN</div>

    <?php if (isset($_SESSION['user'])): 
        $isAdmin = $_SESSION['user']['role'] === 'admin';
        $roleLabel = $isAdmin ? 'Quản Trị Viên' : 'Nhân Viên';
        $roleColor = $isAdmin ? '#52DDB5' : '#ffc107';
        $roleBg    = $isAdmin ? 'rgba(82,221,181,0.12)' : 'rgba(255,193,7,0.12)';
        $roleBorder= $isAdmin ? 'rgba(82,221,181,0.35)' : 'rgba(255,193,7,0.35)';
        $roleIcon  = $isAdmin ? 'fa-user-shield' : 'fa-user';
        $fullname  = htmlspecialchars($_SESSION['user']['fullname'] ?? $_SESSION['user']['username']);
    ?>
    <div style="display:flex; flex-direction:column; align-items:center; padding:18px 15px 20px; margin:0 12px 10px; background:rgba(255,255,255,0.035); border:1px solid rgba(255,255,255,0.07); border-radius:18px; text-align:center;">
        <!-- Avatar -->
        <div style="position:relative; margin-bottom:12px;">
            <div style="width:68px; height:68px; border-radius:50%; background:linear-gradient(135deg,#2A8C6F,#52DDB5); display:flex; align-items:center; justify-content:center; font-size:1.8rem; color:#0d1f18; box-shadow:0 0 0 3px rgba(82,221,181,0.2), 0 6px 20px rgba(82,221,181,0.2);">
                <i class="fas <?php echo $roleIcon; ?>" style="color:#0d1f18;"></i>
            </div>
            <!-- Online dot -->
            <span style="position:absolute; bottom:3px; right:3px; width:12px; height:12px; background:#2ecc71; border-radius:50%; border:2px solid #122119;"></span>
        </div>
        <!-- Name -->
        <div style="font-weight:700; font-size:0.92rem; color:#E6F4EB; margin-bottom:7px; max-width:190px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo $fullname; ?></div>
        <!-- Role Badge -->
        <span style="display:inline-block; padding:4px 14px; border-radius:20px; font-size:0.68rem; font-weight:700; letter-spacing:0.8px; color:<?php echo $roleColor; ?>; background:<?php echo $roleBg; ?>; border:1px solid <?php echo $roleBorder; ?>;">
            <?php echo $roleLabel; ?>
        </span>
    </div>
    <?php endif; ?>


    <!-- MENU CÓ THANH CUỘN -->
    <div class="sidebar-menu-scroll" id="sidebarMenuScroll">
        <ul>
            <?php 
            $current_url = $_GET['url'] ?? 'home';
            function isActive($url, $current) {
                return $url === $current ? 'active' : '';
            }
            ?>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <li>
                <a href="/hi/public/index.php?url=home" class="<?= isActive('home', $current_url) ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            </li>
            <?php endif; ?>
            
            <div class="sidebar-section-title">Quản lý hàng hóa</div>
            <li>
                <a href="/hi/public/index.php?url=product" class="<?= isActive('product', $current_url) ?>">
                    <i class="fas fa-leaf"></i> Sản phẩm
                </a>
            </li>
            <li>
                <a href="/hi/public/index.php?url=category" class="<?= isActive('category', $current_url) ?>">
                    <i class="fas fa-tags"></i> Danh mục
                </a>
            </li>
            <li>
                <a href="/hi/public/index.php?url=supplier" class="<?= isActive('supplier', $current_url) ?>">
                    <i class="fas fa-truck"></i> Nhà cung cấp
                </a>
            </li>

            <div class="sidebar-section-title">Bán hàng & Khách</div>
            <li>
                <a href="/hi/public/index.php?url=pos" class="<?= isActive('pos', $current_url) ?>" style="color: var(--color-mint-light); font-weight: bold;">
                    <i class="fas fa-cash-register"></i> POS Bán hàng
                </a>
            </li>
            <li>
                <a href="/hi/public/index.php?url=order" class="<?= isActive('order', $current_url) ?>">
                    <i class="fas fa-file-invoice-dollar"></i> Đơn hàng
                </a>
            </li>
            <li>
                <a href="/hi/public/index.php?url=customer" class="<?= isActive('customer', $current_url) ?>">
                    <i class="fas fa-user-friends"></i> Khách hàng
                </a>
            </li>

            <div class="sidebar-section-title">Marketing & Hệ thống</div>
            <li>
                <a href="/hi/public/index.php?url=promotion" class="<?= isActive('promotion', $current_url) ?>">
                    <i class="fas fa-gift"></i> Khuyến mãi
                </a>
            </li>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <li>
                <a href="/hi/public/index.php?url=user" class="<?= isActive('user', $current_url) ?>">
                    <i class="fas fa-user-shield"></i> Nhân viên
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Đăng xuất cố định ở dưới cùng -->
        <div class="sidebar-logout">
            <a href="/hi/public/index.php?url=logout" style="color: #ff6b6b;" onclick="showLogoutModal(event)">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </div>

    <!-- Script khôi phục vị trí cuộn -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarScroll = document.getElementById("sidebarMenuScroll");
            if (sidebarScroll) {
                // Khôi phục vị trí
                const scrollPos = sessionStorage.getItem("sidebarScrollPos");
                if (scrollPos) {
                    sidebarScroll.scrollTop = scrollPos;
                }

                // Lưu vị trí khi cuộn
                sidebarScroll.addEventListener("scroll", function() {
                    sessionStorage.setItem("sidebarScrollPos", sidebarScroll.scrollTop);
                });

                // Lưu vị trí khi click link
                const links = sidebarScroll.querySelectorAll("a");
                links.forEach(link => {
                    link.addEventListener("click", function() {
                        sessionStorage.setItem("sidebarScrollPos", sidebarScroll.scrollTop);
                    });
                });
            }
        });
    </script>

</div>