<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-fingerprint fa-2x"></i>
        <span>SAB IPPEK</span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="active">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="usuarios.php">
                <i class="fas fa-users"></i>
                <span>Usuários</span>
            </a>
        </li>
        <li>
            <a href="presencas.php">
                <i class="fas fa-calendar-check"></i>
                <span>Presenças</span>
            </a>
        </li>
        <li>
            <a href="relatorios.php">
                <i class="fas fa-file-alt"></i>
                <span>Relatórios</span>
            </a>
        </li>
        <li>
            <a href="configuracoes.php">
                <i class="fas fa-cog"></i>
                <span>Configurações</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair</span>
        </a>
    </div>
</div>

<style>
.sidebar {
    width: 260px;
    height: 100vh;
    background: var(--sidebar-bg);
    color: var(--sidebar-text);
    position: fixed;
    top: 0;
    left: 0;
    padding: 20px;
    display: flex;
    flex-direction: column;
    z-index: 1000;
    transition: var(--transition);
}

.sidebar-header {
    display: flex;
    align-items: center;
    gap: 15px;
    padding-bottom: 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 30px;
}

.sidebar-header span {
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    font-size: 1.2rem;
}

.sidebar-menu {
    list-style: none;
    flex: 1;
}

.sidebar-menu li {
    margin-bottom: 10px;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 15px;
    color: var(--sidebar-text);
    text-decoration: none;
    border-radius: 10px;
    transition: var(--transition);
    opacity: 0.8;
}

.sidebar-menu a:hover, .sidebar-menu a.active {
    background: rgba(255, 255, 255, 0.1);
    opacity: 1;
}

.sidebar-menu a.active {
    background: var(--secondary-blue);
}

.sidebar-menu i {
    width: 20px;
    text-align: center;
}

.sidebar-footer {
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-footer a {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 15px;
    color: var(--sidebar-text);
    text-decoration: none;
    border-radius: 10px;
    transition: var(--transition);
    opacity: 0.8;
}

.sidebar-footer a:hover {
    background: var(--danger);
    opacity: 1;
}

/* Dashboard Layout */
.main-content {
    margin-left: 260px;
    padding: 30px;
    min-height: 100vh;
    background-color: var(--bg-color);
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--gray-300);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--card-bg);
    padding: 25px;
    border-radius: 20px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 20px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-info h3 {
    font-size: 0.9rem;
    color: var(--gray-600);
    font-weight: 500;
}

.stat-info p {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-color);
}

.chart-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

.chart-card {
    background: var(--card-bg);
    padding: 25px;
    border-radius: 20px;
    box-shadow: var(--shadow);
}

.chart-card h2 {
    font-size: 1.1rem;
    margin-bottom: 20px;
    color: var(--text-color);
}

@media (max-width: 768px) {
    .sidebar {
        width: 70px;
        padding: 10px;
    }
    
    .sidebar-header span, .sidebar-menu span, .sidebar-footer span {
        display: none;
    }
    
    .main-content {
        margin-left: 70px;
    }
    
    .chart-grid {
        grid-template-columns: 1fr;
    }
}
</style>
