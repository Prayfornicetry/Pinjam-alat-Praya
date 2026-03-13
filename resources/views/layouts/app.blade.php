<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2d5016">
    
    <!-- ✅ App Description dari Setting -->
    <meta name="description" content="{{ \App\Models\Setting::get('app_description', 'Sistem Peminjaman Alat Terpadu') }}">
    <meta name="application-name" content="{{ \App\Models\Setting::get('app_name', 'E-Lending System') }}">
    <meta name="author" content="{{ \App\Models\Setting::get('app_name', 'E-Lending System') }}">
    
    <!-- ✅ Favicon dari Setting -->
    @if(\App\Models\Setting::get('app_favicon'))
        <link rel="icon" href="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" type="image/x-icon">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📦</text></svg>">
    @endif
    
    <title>@yield('title', 'Dashboard') - {{ \App\Models\Setting::get('app_name', 'E-Lending System') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Custom CSS -->
<style>
        /* ==================== CSS VARIABLES - NATURAL BALANCED THEME ==================== */
        :root {
            --sidebar-width: 280px;
            --sidebar-width-collapsed: 80px;
            --topbar-height: 70px;
            
            /* 🌿 Natural Earth Colors (Balanced - Not Too Bright/Dark) */
            --primary-color: #556b2f;
            --primary-light: #6b8e23;
            --primary-dark: #3d4f21;
            --secondary-color: #8b7355;
            --success-color: #6b8e23;
            --warning-color: #daa520;
            --danger-color: #a52a2a;
            --info-color: #5f9ea0;
            
            /* 🍃 Natural Nature Gradients (Subtle & Balanced) */
            --primary-gradient: linear-gradient(135deg, #6b8e23 0%, #556b2f 100%);
            --primary-gradient-hover: linear-gradient(135deg, #556b2f 0%, #6b8e23 100%);
            --secondary-gradient: linear-gradient(135deg, #8b7355 0%, #6d5a43 100%);
            --success-gradient: linear-gradient(135deg, #7d9f3f 0%, #6b8e23 100%);
            --warning-gradient: linear-gradient(135deg, #e6c86e 0%, #daa520 100%);
            --danger-gradient: linear-gradient(135deg, #c05040 0%, #a52a2a 100%);
            --info-gradient: linear-gradient(135deg, #7fb3b5 0%, #5f9ea0 100%);
            --forest-gradient: linear-gradient(135deg, #6b8e23 0%, #556b2f 50%, #3d4f21 100%);
            --earth-gradient: linear-gradient(135deg, #a08060 0%, #8b7355 100%);
            
            /* 🌲 Natural Sidebar (Balanced Forest Green) */
            --sidebar-bg: linear-gradient(180deg, #2d3a1e 0%, #3d4f21 50%, #556b2f 100%);
            --sidebar-hover: rgba(107, 142, 35, 0.25);
            --sidebar-active: linear-gradient(90deg, rgba(107, 142, 35, 0.35) 0%, transparent 100%);
            
            /* 🍂 Natural Background (Soft Earth Tone - Balanced) */
            --body-bg: linear-gradient(135deg, #f5f5f0 0%, #e8ece5 50%, #e0e5dc 100%);
            --card-bg: rgba(255, 255, 255, 0.97);
            --card-header-bg: linear-gradient(135deg, #ffffff 0%, #f5f5f0 100%);
            
            /* ✏️ Text Colors (Balanced Contrast) */
            --text-primary: #2d3a1e;
            --text-secondary: #3d4f21;
            --text-muted: #6b7b5f;
            --text-light: #ffffff;
            
            /* 💫 Balanced Shadows (Natural Depth) */
            --shadow-sm: 0 2px 8px rgba(85, 107, 47, 0.12);
            --shadow-md: 0 4px 20px rgba(85, 107, 47, 0.15);
            --shadow-lg: 0 10px 40px rgba(85, 107, 47, 0.18);
            --shadow-xl: 0 20px 60px rgba(85, 107, 47, 0.22);
            --shadow-glow: 0 0 25px rgba(107, 142, 35, 0.35);
            --shadow-neon: 0 0 15px rgba(107, 142, 35, 0.4), 0 0 30px rgba(85, 107, 47, 0.3);
            
            /* 🔲 Balanced Borders (Subtle) */
            --border-light: rgba(107, 142, 35, 0.25);
            --border-medium: rgba(107, 142, 35, 0.4);
            --border-dark: rgba(85, 107, 47, 0.6);
            
            /* ⚡ Transitions */
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-bounce: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        /* ==================== BASE STYLES ==================== */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            background-attachment: fixed;
            overflow-x: hidden;
            font-size: 14px;
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* ✨ Subtle Natural Background (No Animation - More Calm) */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(107, 142, 35, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(85, 107, 47, 0.08) 0%, transparent 50%);
            z-index: -1;
        }

        /* ==================== SIDEBAR - NATURAL BALANCED ==================== */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-xl);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.08); }
        .sidebar::-webkit-scrollbar-thumb { 
            background: var(--primary-light); 
            border-radius: 10px;
        }

        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2px solid rgba(255,255,255,0.15);
            padding: 0 20px;
            background: rgba(255,255,255,0.05);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .sidebar-brand img { 
            max-height: 40px; 
            margin-right: 12px;
            transition: var(--transition-smooth);
        }

        .sidebar-brand img:hover { 
            transform: scale(1.05); 
        }

        .sidebar-brand h3 { 
            color: var(--text-light); 
            font-size: 1.1rem; 
            font-weight: 800; 
            margin: 0;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .sidebar-menu { padding: 15px 0; }

        .sidebar-menu .menu-label {
            color: rgba(255,255,255,0.5);
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 12px 20px 8px;
            margin-top: 10px;
            letter-spacing: 1.5px;
            position: relative;
        }

        .sidebar-menu .menu-label::before {
            content: '';
            position: absolute;
            left: 20px;
            bottom: 3px;
            width: 25px;
            height: 2px;
            background: var(--primary-light);
            border-radius: 2px;
        }

        .sidebar-menu a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: var(--transition-smooth);
            border-left: 4px solid transparent;
            font-size: 0.85rem;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar-menu a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: var(--sidebar-hover);
            transition: var(--transition-smooth);
            z-index: 0;
        }

        .sidebar-menu a:hover::before {
            width: 100%;
        }

        .sidebar-menu a:hover {
            color: var(--text-light);
            border-left-color: var(--primary-light);
            padding-left: 25px;
        }

        .sidebar-menu a:hover i {
            transform: scale(1.15);
        }

        .sidebar-menu a.active {
            color: var(--text-light);
            background: var(--sidebar-active);
            border-left-color: var(--primary-light);
        }

        .sidebar-menu a.active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            width: 3px;
            height: 100%;
            background: var(--primary-light);
        }

        .sidebar-menu a.active i { 
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.85; }
        }

        .sidebar-menu a i { 
            margin-right: 12px; 
            width: 20px; 
            text-align: center;
            font-size: 1rem;
            transition: var(--transition-smooth);
            position: relative;
            z-index: 1;
        }

        .sidebar-menu a .badge { 
            margin-left: auto; 
            font-size: 0.65rem; 
            padding: 3px 8px;
            border-radius: 15px;
            font-weight: 600;
            background: var(--warning-gradient);
            box-shadow: 0 2px 8px rgba(218, 165, 32, 0.4);
        }

        /* ==================== MAIN CONTENT ==================== */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: var(--transition-smooth);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ==================== TOPBAR - NATURAL BALANCED ==================== */
        .topbar {
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px) saturate(180%);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 2px solid var(--border-light);
        }

        .toggle-sidebar {
            background: var(--primary-gradient);
            border: none;
            font-size: 1.3rem;
            cursor: pointer;
            color: var(--text-light);
            padding: 8px;
            border-radius: 10px;
            transition: var(--transition-bounce);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-sidebar:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .topbar-nav { display: flex; align-items: center; gap: 10px; }

        .notification-btn {
            position: relative;
            background: var(--primary-gradient);
            border: none;
            font-size: 1.2rem;
            color: var(--text-light);
            cursor: pointer;
            padding: 8px;
            border-radius: 10px;
            transition: var(--transition-bounce);
            box-shadow: var(--shadow-sm);
        }

        .notification-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: var(--shadow-md);
        }

        .notification-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            background: var(--danger-gradient);
            color: var(--text-light);
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 15px;
            min-width: 18px;
            text-align: center;
            font-weight: 700;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(165, 42, 42, 0.4);
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 12px;
            transition: var(--transition-bounce);
            background: var(--card-header-bg);
            border: 2px solid var(--border-light);
        }

        .user-dropdown:hover {
            background: var(--primary-gradient);
            border-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .user-dropdown:hover .user-info h6 { color: var(--text-light); }
        .user-dropdown:hover .user-info small { color: rgba(255,255,255,0.9); }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
            box-shadow: var(--shadow-sm);
        }

        .user-dropdown:hover .user-avatar {
            border-color: var(--text-light);
            transform: scale(1.1);
        }

        .user-info h6 { 
            margin: 0; 
            font-size: 0.8rem; 
            font-weight: 700; 
            color: var(--text-primary);
            transition: var(--transition-smooth);
            white-space: nowrap;
        }

        .user-info small { 
            font-size: 0.65rem; 
            color: var(--text-muted);
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        /* ==================== CONTENT AREA ==================== */
        .content-area { 
            padding: 25px; 
            flex: 1;
        }

        /* ==================== CARDS - NATURAL BALANCED ==================== */
        .card {
            border: 2px solid var(--border-light);
            border-radius: 20px;
            background: var(--card-bg);
            box-shadow: var(--shadow-md);
            margin-bottom: 20px;
            transition: var(--transition-bounce);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--forest-gradient);
            opacity: 0;
            transition: var(--transition-smooth);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--border-medium);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card-header {
            background: var(--card-header-bg);
            border-bottom: 2px solid var(--border-light);
            padding: 15px 20px;
            font-weight: 700;
            color: var(--primary-color);
            font-size: 0.95rem;
        }

        .card-body { padding: 20px; }

        /* ==================== TABLES - NATURAL BALANCED ==================== */
        .table-responsive {
            border-radius: 15px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 2px solid var(--border-light);
            background: var(--card-bg);
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .table {
            margin-bottom: 0;
            min-width: 100%;
            font-size: 0.85rem;
        }

        .table thead th {
            background: var(--primary-gradient);
            color: var(--text-light);
            border: none;
            padding: 10px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .table tbody tr {
            transition: var(--transition-smooth);
            border-bottom: 1px solid var(--border-light);
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, rgba(107, 142, 35, 0.06) 0%, transparent 100%);
        }

        .table tbody td {
            padding: 10px 12px;
            vertical-align: middle;
            color: var(--text-primary);
            border-color: var(--border-light);
            font-size: 0.85rem;
            line-height: 1.4;
        }

        /* ==================== STAT CARDS - NATURAL BALANCED ==================== */
        .stat-card {
            background: var(--card-bg);
            border: 2px solid var(--border-light);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--shadow-md);
            transition: var(--transition-bounce);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--border-medium);
        }

        .stat-card.primary {
            background: var(--forest-gradient);
            color: var(--text-light);
            border: none;
            box-shadow: var(--shadow-glow);
        }

        .stat-card.success {
            background: var(--success-gradient);
            color: var(--text-light);
            border: none;
            box-shadow: 0 0 25px rgba(107, 142, 35, 0.35);
        }

        .stat-card.warning {
            background: var(--warning-gradient);
            color: var(--text-light);
            border: none;
            box-shadow: 0 0 25px rgba(218, 165, 32, 0.35);
        }

        .stat-card.danger {
            background: var(--danger-gradient);
            color: var(--text-light);
            border: none;
            box-shadow: 0 0 25px rgba(165, 42, 42, 0.35);
        }

        .stat-card.info {
            background: var(--info-gradient);
            color: var(--text-light);
            border: none;
            box-shadow: 0 0 25px rgba(95, 158, 160, 0.35);
        }

        .stat-card h3 {
            font-size: 1.75rem;
            font-weight: 800;
            margin: 8px 0;
        }

        .stat-card h6 {
            font-size: 0.75rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ==================== BADGES - NATURAL BALANCED ==================== */
        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.7rem;
            border: none;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .bg-primary { 
            background: var(--primary-gradient) !important; 
            color: var(--text-light) !important;
            box-shadow: 0 2px 8px rgba(107, 142, 35, 0.35);
        }

        .bg-success { 
            background: var(--success-gradient) !important; 
            color: var(--text-light) !important;
            box-shadow: 0 2px 8px rgba(107, 142, 35, 0.35);
        }

        .bg-warning { 
            background: var(--warning-gradient) !important; 
            color: white !important;
            box-shadow: 0 2px 8px rgba(218, 165, 32, 0.35);
        }

        .bg-danger { 
            background: var(--danger-gradient) !important; 
            color: var(--text-light) !important;
            box-shadow: 0 2px 8px rgba(165, 42, 42, 0.35);
        }

        .bg-info { 
            background: var(--info-gradient) !important; 
            color: var(--text-light) !important;
            box-shadow: 0 2px 8px rgba(95, 158, 160, 0.35);
        }

        .bg-secondary { 
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important; 
            color: var(--text-light) !important;
        }

        /* ==================== BUTTONS - NATURAL BALANCED ==================== */
        .btn {
            border-radius: 12px;
            padding: 8px 16px;
            font-weight: 600;
            transition: var(--transition-bounce);
            border: none;
            font-size: 0.85rem;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::after {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: var(--text-light);
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: var(--success-gradient);
            color: var(--text-light);
            box-shadow: var(--shadow-sm);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(107, 142, 35, 0.4);
        }

        .btn-warning {
            background: var(--warning-gradient);
            color: var(--text-light);
            box-shadow: var(--shadow-sm);
        }

        .btn-info {
            background: var(--info-gradient);
            color: var(--text-light);
            box-shadow: var(--shadow-sm);
        }

        .btn-danger {
            background: var(--danger-gradient);
            color: var(--text-light);
            box-shadow: var(--shadow-sm);
        }

        .btn-outline-primary {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-gradient);
            border-color: transparent;
            color: var(--text-light);
            box-shadow: var(--shadow-md);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.75rem;
        }

        /* ==================== FLASH MESSAGES - NATURAL BALANCED ==================== */
        .flash-message {
            position: fixed;
            top: 85px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
            max-width: 400px;
            animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        @keyframes slideInRight {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .flash-message .alert {
            border-radius: 15px;
            border: none;
            box-shadow: var(--shadow-lg);
            color: var(--text-light);
            font-weight: 500;
            padding: 12px 15px;
            font-size: 0.85rem;
        }

        .flash-message .alert-success { 
            background: var(--success-gradient);
            box-shadow: 0 5px 20px rgba(107, 142, 35, 0.35);
        }

        .flash-message .alert-danger { 
            background: var(--danger-gradient);
            box-shadow: 0 5px 20px rgba(165, 42, 42, 0.35);
        }

        .flash-message .alert-warning { 
            background: var(--warning-gradient);
            box-shadow: 0 5px 20px rgba(218, 165, 32, 0.35);
        }

        /* ==================== FOOTER ==================== */
        footer {
            background: var(--card-bg);
            border-top: 2px solid var(--border-light);
            padding: 15px;
            text-align: center;
            font-size: 0.8rem;
        }

        /* ==================== DROPDOWN - NATURAL BALANCED ==================== */
        .dropdown-menu {
            border-radius: 15px;
            box-shadow: var(--shadow-xl);
            border: 2px solid var(--border-light);
            padding: 8px 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            min-width: 200px;
            animation: dropdownSlide 0.3s ease;
        }

        @keyframes dropdownSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            padding: 10px 15px;
            font-size: 0.8rem;
            transition: var(--transition-smooth);
            color: var(--text-primary);
        }

        .dropdown-item:hover {
            background: linear-gradient(90deg, rgba(107, 142, 35, 0.1) 0%, transparent 100%);
            color: var(--primary-color);
            padding-left: 20px;
        }

        .dropdown-item i { width: 20px; text-align: center; color: var(--primary-color); }
        .dropdown-item.text-danger { color: var(--danger-color); }
        .dropdown-item.text-danger:hover { 
            background: var(--danger-gradient); 
            color: var(--text-light);
            box-shadow: 0 2px 8px rgba(165, 42, 42, 0.35);
        }
        .dropdown-item.text-danger:hover i { color: var(--text-light); }

        .dropdown-divider {
            margin: 6px 0;
            border-top: 1px solid var(--border-light);
        }

        .dropdown-header {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            padding: 8px 15px;
        }

        /* ==================== FORMS - NATURAL BALANCED ==================== */
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid var(--border-light);
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: var(--transition-smooth);
            background: rgba(255, 255, 255, 0.95);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.2rem rgba(107, 142, 35, 0.2);
            outline: none;
            background: white;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (min-width: 1400px) {
            :root { --sidebar-width: 300px; }
            .content-area { padding: 35px; }
            .stat-card h3 { font-size: 2rem; }
        }

        @media (min-width: 992px) and (max-width: 1399px) {
            :root { --sidebar-width: 260px; }
            .content-area { padding: 25px; }
        }

        @media (max-width: 991px) {
            :root { --sidebar-width: 240px; }
            .sidebar-brand h3 { font-size: 1rem; }
            .sidebar-menu a { padding: 10px 15px; font-size: 0.8rem; }
            .content-area { padding: 20px; }
            .topbar { padding: 0 20px; }
            .stat-card h3 { font-size: 1.5rem; }
            .table { font-size: 0.8rem; }
        }

        @media (max-width: 768px) {
            .sidebar { margin-left: calc(var(--sidebar-width) * -1); box-shadow: none; }
            .sidebar.active { margin-left: 0; box-shadow: var(--shadow-xl); }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                backdrop-filter: blur(5px);
                z-index: 998;
                opacity: 0;
                transition: opacity 0.3s;
            }
            .sidebar-overlay.active { display: block; opacity: 1; }
            .main-content { margin-left: 0; }
            .user-info { display: none; }
            .topbar { padding: 0 15px; height: 60px; }
            .content-area { padding: 15px; }
            .flash-message { left: 15px; right: 15px; min-width: auto; max-width: none; }
            .stat-card { margin-bottom: 15px; }
            .stat-card h3 { font-size: 1.5rem; }
            .table-responsive { font-size: 0.75rem; }
            .btn { padding: 6px 12px; font-size: 0.75rem; }
        }

        @media (max-width: 576px) {
            .sidebar { width: 260px; }
            .sidebar-brand { padding: 0 15px; }
            .sidebar-brand h3 { font-size: 0.95rem; }
            .sidebar-brand img { max-height: 35px; }
            .topbar-nav { gap: 5px; }
            .user-avatar { width: 32px; height: 32px; }
            .notification-btn, .toggle-sidebar { padding: 6px; font-size: 1.1rem; }
            .content-area { padding: 12px; }
            .card { border-radius: 12px; }
            .card-header { padding: 12px 15px; font-size: 0.9rem; }
            .card-body { padding: 15px; }
            h4 { font-size: 1.1rem; }
            h5 { font-size: 1rem; }
            .flash-message { top: 70px; }
            .dropdown-menu { min-width: 180px; }
            .stat-card h3 { font-size: 1.25rem; }
            .stat-card h6 { font-size: 0.7rem; }
            .table thead th { padding: 10px 12px; font-size: 0.65rem; }
            .table tbody td { padding: 10px 12px; font-size: 0.8rem; }
        }

        @media (max-width: 380px) {
            .sidebar { width: 240px; }
            .sidebar-brand h3 { font-size: 0.85rem; }
            .user-avatar { width: 28px; height: 28px; }
            .content-area { padding: 10px; }
            .stat-card { padding: 15px; }
            .stat-card h3 { font-size: 1.1rem; }
        }

        /* ==================== UTILITY CLASSES ==================== */
        .hover-lift { 
            transition: var(--transition-bounce); 
        }
        .hover-lift:hover { 
            transform: translateY(-5px); 
            box-shadow: var(--shadow-lg); 
        }

        .gradient-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .text-primary-custom { color: var(--primary-color) !important; }
        .text-success-custom { color: var(--success-color) !important; }
        .text-warning-custom { color: var(--warning-color) !important; }
        .text-danger-custom { color: var(--danger-color) !important; }

        /* Notification Read State */
        .dropdown-item.opacity-50 {
            opacity: 0.5 !important;
            background: rgba(0, 0, 0, 0.02);
        }

        .dropdown-item.opacity-50:hover {
            opacity: 0.7 !important;
        }

        /* Print Styles */
        @media print {
            .sidebar, .topbar, .flash-message { display: none !important; }
            .main-content { margin-left: 0; }
            .content-area { padding: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            @if(\App\Models\Setting::get('app_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" alt="Logo">
            @elseif(\App\Models\Setting::get('app_favicon'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" alt="Logo" style="max-height: 35px;">
            @else
                <i class="bi bi-box-seam-fill fs-2" style="color: #4a7c23;"></i>
            @endif
            <h3>{{ \App\Models\Setting::get('app_name', 'E-Lending System') }}</h3>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-label">Main Menu</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            
            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
            <div class="menu-label">Master Data</div>
            <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Data Alat
            </a>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder"></i> Kategori
            </a>
            @endif
            
            @if(Auth::user()->isAdmin())
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Pengguna
            </a>
            @endif
            
            @if(Auth::user()->isUser())
            <div class="menu-label">Katalog</div>
            <a href="{{ route('items.user.index') }}" class="{{ request()->routeIs('items.user.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap"></i> Data Alat
            </a>
            @endif
            
            <div class="menu-label">Transaksi</div>
            @if(Auth::user()->isUser())
                <a href="{{ route('borrowings.my') }}" class="{{ request()->routeIs('borrowings.my') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i> Peminjaman Saya
                </a>
                <a href="{{ route('borrowing.request.create') }}" class="{{ request()->routeIs('borrowing.request.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i> Ajukan Peminjaman
                </a>
            @else
                <a href="{{ route('borrowings.index') }}" class="{{ request()->routeIs('borrowings.*') && !request()->routeIs('borrowings.history') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i> Peminjaman
                    @php $pendingCount = \App\Models\Borrowing::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="badge bg-danger">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endif
            
            <a href="{{ route('borrowings.history') }}" class="{{ request()->routeIs('borrowings.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Riwayat
            </a>
            
            @if(Auth::user()->isAdmin())
            <div class="menu-label">Lainnya</div>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan
            </a>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
            @endif
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <button class="toggle-sidebar" id="toggleSidebar" title="Toggle Menu">
                <i class="bi bi-list"></i>
            </button>

            <div class="topbar-nav">
                <!-- ✅ Notification Dropdown (FIXED) -->
                <button class="notification-btn" data-bs-toggle="dropdown" title="Notifikasi" id="notificationButton" onclick="markAllAsRead()">
                    <i class="bi bi-bell"></i>
                    @php
                        $clickedNotifications = json_decode(session('clicked_notifications', '[]'), true) ?? [];
                        $approvedBorrowings = \App\Models\Borrowing::where('user_id', Auth::id())
                            ->where('status', 'approved')
                            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))
                            ->get();
                        $unreadNotifications = $approvedBorrowings->filter(function($b) use ($clickedNotifications) {
                            return !in_array('approved_' . $b->id, $clickedNotifications);
                        });
                        $notificationCount = $unreadNotifications->count();
                    @endphp
                    @if($notificationCount > 0)
                        <span class="notification-badge" id="notificationBadge">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px;" id="notificationDropdown">
                    <li><h6 class="dropdown-header fw-bold">🔔 Notifikasi Approval</h6></li>
                    @php
                        $approvedBorrowings = \App\Models\Borrowing::where('user_id', Auth::id())
                            ->where('status', 'approved')
                            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))
                            ->with('item')
                            ->latest()
                            ->take(5)
                            ->get();
                        $clickedNotifications = json_decode(session('clicked_notifications', '[]'), true) ?? [];
                    @endphp
                    @forelse($approvedBorrowings as $borrowing)
                        @php
                            $notifId = 'approved_' . $borrowing->id;
                            $isRead = in_array($notifId, $clickedNotifications);
                        @endphp
                    <li>
                        <a class="dropdown-item {{ $isRead ? 'opacity-50' : '' }}" 
                           href="{{ route('borrowings.my.show', $borrowing->id) }}"
                           onclick="markAsRead('{{ $notifId }}'); event.stopPropagation();">
                            <small>
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Peminjaman Disetujui!</strong><br>
                                <span class="text-muted">{{ $borrowing->item->name ?? 'Alat' }}</span><br>
                                <span class="text-muted small">Kembali: {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</span>
                            </small>
                        </a>
                    </li>
                    @empty
                    <li><a class="dropdown-item text-center text-muted" href="#"><i class="bi bi-check-circle me-2"></i>Tidak ada notifikasi approval baru</a></li>
                    @endforelse
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-center small fw-bold" href="{{ route('borrowings.my') }}" onclick="markAllAsRead(); event.stopPropagation();">
                            Lihat Semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </li>
                </ul>

                <!-- ✅ User Profile Dropdown (FIXED) -->
                <div class="dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown">
                        @if(Auth::user()->avatar && file_exists(public_path('storage/' . Auth::user()->avatar)))
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="User" class="user-avatar">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=2d5016&color=fff" 
                                 alt="User" class="user-avatar">
                        @endif
                        <div class="user-info d-none d-md-block">
                            <h6>{{ Auth::user()->name ?? 'Admin' }}</h6>
                            <small>{{ ucfirst(Auth::user()->role ?? 'Administrator') }}</small>
                        </div>
                        <i class="bi bi-chevron-down ms-2 small d-none d-md-block"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px;">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                <i class="bi bi-person-circle me-2"></i>Profil Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-pencil-square me-2"></i>Edit Profil
                            </a>
                        </li>
                        @if(Auth::user()->isAdmin())
                        <li>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="bi bi-gear me-2"></i>Pengaturan
                            </a>
                        </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="flash-message">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="flash-message">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="flash-message">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <div class="content-area">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="mt-auto">
            <small class="text-muted">{!! \App\Models\Setting::get('app_footer', '© ' . date('Y') . ' ' . \App\Models\Setting::get('app_name', 'E-Lending System') . '. All rights reserved.') !!}</small>
        </footer>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Custom JS (FIXED) -->
    <script>
    // Toggle Sidebar
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleMenu() {
        sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') && window.innerWidth <= 768 ? 'hidden' : '';
    }

    if (toggleSidebar) {
        toggleSidebar.addEventListener('click', toggleMenu);
    }

    if (overlay) {
        overlay.addEventListener('click', toggleMenu);
    }

    // Auto hide flash messages after 5 seconds
    setTimeout(function() {
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(function(message) {
            message.style.opacity = '0';
            message.style.transform = 'translateX(400px)';
            message.style.transition = 'all 0.4s ease';
            setTimeout(() => message.remove(), 400);
        });
    }, 5000);

    // Close sidebar on mobile when clicking menu item
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('.sidebar-menu a');
        links.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    setTimeout(toggleMenu, 200);
                }
            });
        });
        
        // ✅ Load read notifications on page load
        loadReadNotifications();
    });

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }, 250);
    });

    // Prevent double-tap zoom on mobile
    document.addEventListener('dblclick', function(event) {
        event.preventDefault();
    }, { passive: false });

    // ✅ Mark Single Notification as Read
    function markAsRead(notifId) {
        // Get current read notifications from localStorage
        let clickedNotifications = JSON.parse(localStorage.getItem('clicked_notifications') || '[]');
        
        // Add new notification ID if not exists
        if (!clickedNotifications.includes(notifId)) {
            clickedNotifications.push(notifId);
            localStorage.setItem('clicked_notifications', JSON.stringify(clickedNotifications));
        }
        
        // Also send to server (session-based)
        fetch('{{ route("notifications.mark-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ notification_id: notifId })
        }).catch(error => console.error('Error:', error));
        
        // Hide badge if all read
        checkAndHideBadge();
        
        // Fade out the clicked notification
        const dropdownItems = document.querySelectorAll('#notificationDropdown .dropdown-item');
        dropdownItems.forEach(item => {
            const onclickAttr = item.getAttribute('onclick');
            if (onclickAttr && onclickAttr.includes(notifId)) {
                item.classList.add('opacity-50');
            }
        });
    }

    // ✅ Mark All Notifications as Read
    function markAllAsRead() {
        // Get all notification IDs from dropdown
        const dropdownItems = document.querySelectorAll('#notificationDropdown .dropdown-item[href*="borrowings.my.show"]');
        let clickedNotifications = JSON.parse(localStorage.getItem('clicked_notifications') || '[]');
        
        dropdownItems.forEach(item => {
            const onclickAttr = item.getAttribute('onclick');
            if (onclickAttr) {
                const match = onclickAttr.match(/markAsRead\('([^']+)'\)/);
                if (match && !clickedNotifications.includes(match[1])) {
                    clickedNotifications.push(match[1]);
                }
            }
        });
        
        // Save to localStorage
        localStorage.setItem('clicked_notifications', JSON.stringify(clickedNotifications));
        
        // Send to server
        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).catch(error => console.error('Error:', error));
        
        // Hide badge
        hideBadge();
        
        // Fade out all read notifications
        dropdownItems.forEach(item => {
            item.classList.add('opacity-50');
        });
    }

    // ✅ Check and Hide Badge
    function checkAndHideBadge() {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            const clickedNotifications = JSON.parse(localStorage.getItem('clicked_notifications') || '[]');
            const dropdownItems = document.querySelectorAll('#notificationDropdown .dropdown-item[href*="borrowings.my.show"]');
            
            let unreadCount = 0;
            dropdownItems.forEach(item => {
                const onclickAttr = item.getAttribute('onclick');
                if (onclickAttr) {
                    const match = onclickAttr.match(/markAsRead\('([^']+)'\)/);
                    if (match && !clickedNotifications.includes(match[1])) {
                        unreadCount++;
                    }
                }
            });
            
            if (unreadCount === 0) {
                hideBadge();
            } else {
                badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
            }
        }
    }

    // ✅ Hide Badge
    function hideBadge() {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            badge.style.display = 'none';
        }
    }

    // ✅ Load Read Notifications
    function loadReadNotifications() {
        const clickedNotifications = JSON.parse(localStorage.getItem('clicked_notifications') || '[]');
        const dropdownItems = document.querySelectorAll('#notificationDropdown .dropdown-item[href*="borrowings.my.show"]');
        
        dropdownItems.forEach(item => {
            const onclickAttr = item.getAttribute('onclick');
            if (onclickAttr) {
                const match = onclickAttr.match(/markAsRead\('([^']+)'\)/);
                if (match && clickedNotifications.includes(match[1])) {
                    item.classList.add('opacity-50');
                }
            }
        });
        
        // Check if need to hide badge
        checkAndHideBadge();
    }
    </script>

    @stack('scripts')
</body>
</html>