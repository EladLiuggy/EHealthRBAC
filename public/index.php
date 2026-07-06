<?php require_once __DIR__ . '/../app/bootstrap.php'; renderHeader('Home'); ?>
<style>
:root{
    --primary:#0f766e;
    --primary-2:#14b8a6;
    --blue:#2563eb;
    --ink:#0b1220;
    --text:#334155;
    --muted:#64748b;
    --soft:#ecfeff;
    --soft-blue:#eff6ff;
    --white:#ffffff;
    --border:#e2e8f0;
    --shadow:0 24px 70px rgba(15,23,42,.12);
    --radius:30px;
}
html{scroll-behavior:smooth}
.container{max-width:1200px!important;padding-bottom:0!important}
.footer{margin-top:0!important}

/* Animations */
@keyframes fadeUp{from{opacity:0;transform:translateY(36px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeLeft{from{opacity:0;transform:translateX(-42px)}to{opacity:1;transform:translateX(0)}}
@keyframes fadeRight{from{opacity:0;transform:translateX(42px)}to{opacity:1;transform:translateX(0)}}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
@keyframes shine{0%{transform:translateX(-120%)}100%{transform:translateX(120%)}}
@keyframes softBounce{0%,100%{transform:translateY(0)}45%{transform:translateY(-10px)}70%{transform:translateY(3px)}}
@keyframes smallBounce{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-7px) scale(1.03)}}
@keyframes badgeBounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-5px)}}
.bouncy{animation:softBounce 3.2s ease-in-out infinite}
.bouncy-small{animation:smallBounce 2.8s ease-in-out infinite}
.hero-kicker,.hero-points span,.feature-icon,.user-card i,.step-no,.trust-item i{animation:badgeBounce 2.9s ease-in-out infinite}
.feature-card:nth-child(2) .feature-icon,.user-card:nth-child(2) i,.trust-item:nth-child(2) i{animation-delay:.25s}
.feature-card:nth-child(3) .feature-icon,.user-card:nth-child(3) i,.trust-item:nth-child(3) i{animation-delay:.5s}
.feature-card:nth-child(4) .feature-icon,.user-card:nth-child(4) i,.trust-item:nth-child(4) i{animation-delay:.75s}

.reveal,.reveal-left,.reveal-right{opacity:0}
.reveal.visible{animation:fadeUp .8s ease forwards}
.reveal-left.visible{animation:fadeLeft .85s ease forwards}
.reveal-right.visible{animation:fadeRight .85s ease forwards}

/* Buttons */
.btn-primary-pro,.btn-secondary-pro{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:.55rem;
    padding:.9rem 1.25rem;
    border-radius:999px;
    font-weight:850;
    text-decoration:none;
    transition:.25s ease;
}
.btn-primary-pro{
    color:white;
    background:linear-gradient(135deg,var(--primary),var(--blue));
    box-shadow:0 16px 38px rgba(15,118,110,.25);
}
.btn-primary-pro:hover{
    color:white;
    transform:translateY(-3px);
    box-shadow:0 22px 45px rgba(15,118,110,.32);
}
.btn-secondary-pro{
    color:var(--ink);
    background:white;
    border:1px solid var(--border);
}
.btn-secondary-pro:hover{
    color:var(--primary);
    transform:translateY(-3px);
    border-color:#99f6e4;
}

/* Hero */
.landing-hero{
    position:relative;
    min-height:640px;
    display:grid;
    grid-template-columns:1.02fr .98fr;
    align-items:center;
    gap:2.2rem;
    padding:2.6rem 0 1.8rem;
    overflow:hidden;
}
.landing-hero:before{
    content:"";
    position:absolute;
    inset:-150px -80px auto auto;
    width:520px;height:520px;
    border-radius:50%;
    background:radial-gradient(circle,#dbeafe 0%,#ccfbf1 45%,transparent 72%);
    z-index:-2;
}
.landing-hero:after{
    content:"";
    position:absolute;
    left:-180px;bottom:40px;
    width:430px;height:430px;
    border-radius:50%;
    background:radial-gradient(circle,#ccfbf1 0%,transparent 70%);
    z-index:-2;
}
.hero-kicker{
    display:inline-flex;
    align-items:center;
    gap:.55rem;
    color:#115e59;
    background:#ccfbf1;
    border:1px solid #99f6e4;
    padding:.58rem .9rem;
    border-radius:999px;
    font-weight:900;
}
.hero-copy h1{
    margin:1.1rem 0 1rem;
    font-size:3.85rem;
    line-height:.98;
    letter-spacing:-.065em;
    color:var(--ink);
}
.hero-copy p{
    max-width:620px;
    color:var(--muted);
    font-size:1.13rem;
    line-height:1.75;
}
.hero-actions{
    display:flex;
    flex-wrap:wrap;
    gap:1rem;
    margin-top:1.6rem;
}
.hero-points{
    display:flex;
    flex-wrap:wrap;
    gap:.75rem;
    margin-top:1rem;
}
.hero-points span{
    display:inline-flex;
    align-items:center;
    gap:.4rem;
    color:#0f766e;
    background:white;
    border:1px solid var(--border);
    border-radius:999px;
    padding:.55rem .75rem;
    font-weight:750;
    font-size:.92rem;
}
.hero-visual{
    position:relative;
    min-height:500px;
}
.hero-image-card{
    position:absolute;
    inset:15px 0 0 42px;
    border-radius:38px;
    overflow:hidden;
    box-shadow:var(--shadow);
    animation:float 5.5s ease-in-out infinite;
}
.hero-image-card img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}
.hero-image-card:after{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(180deg,rgba(15,23,42,.04),rgba(15,23,42,.18));
}
.doctor-card{
    position:absolute;
    left:0;
    bottom:24px;
    width:295px;
    padding:1rem;
    border-radius:28px;
    background:rgba(255,255,255,.9);
    backdrop-filter:blur(14px);
    border:1px solid rgba(255,255,255,.78);
    box-shadow:0 24px 52px rgba(15,23,42,.16);
    z-index:3;
}
.doctor-card .top{
    display:flex;
    align-items:center;
    gap:.85rem;
}
.doctor-card img{
    width:54px;height:54px;border-radius:18px;object-fit:cover;
}
.doctor-card strong{display:block;color:var(--ink)}
.doctor-card small{color:var(--muted)}
.doctor-card .bar{
    margin-top:.9rem;
    height:9px;
    border-radius:999px;
    background:#e2e8f0;
    overflow:hidden;
}
.doctor-card .bar span{
    display:block;
    width:78%;
    height:100%;
    background:linear-gradient(90deg,var(--primary),var(--blue));
    border-radius:999px;
}
.secure-bubble{
    position:absolute;
    top:50px;
    right:5px;
    width:175px;
    padding:1rem;
    background:white;
    border:1px solid var(--border);
    border-radius:26px;
    box-shadow:0 20px 42px rgba(15,23,42,.13);
    z-index:3;
}
.secure-bubble i{font-size:1.7rem;color:var(--primary)}
.secure-bubble strong{display:block;margin-top:.45rem;color:var(--ink)}
.secure-bubble span{display:block;color:var(--muted);font-size:.88rem;margin-top:.2rem}

/* Section basics */
.section-pro{
    padding:2.6rem 0;
}
.section-title{
    max-width:780px;
    margin:0 auto 1.45rem;
    text-align:center;
}
.section-title .eyebrow{
    display:inline-block;
    color:var(--primary);
    font-size:.82rem;
    letter-spacing:.11em;
    text-transform:uppercase;
    font-weight:950;
    margin-bottom:.35rem;
}
.section-title h2{
    color:var(--ink);
    font-size:2.2rem;
    line-height:1.08;
    letter-spacing:-.04em;
    margin:0 0 .45rem;
}
.section-title p{
    color:var(--muted);
    font-size:1.05rem;
    line-height:1.7;
    margin:0;
}

/* Trust strip */
.trust-strip{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:1rem;
    margin-top:1rem;
}
.trust-item{
    position:relative;
    overflow:hidden;
    background:white;
    border:1px solid var(--border);
    border-radius:24px;
    padding:1.15rem;
    box-shadow:0 14px 32px rgba(15,23,42,.055);
    transition:.25s ease;
}
.trust-item:before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(100deg,transparent,rgba(20,184,166,.11),transparent);
    transform:translateX(-120%);
}
.trust-item:hover:before{animation:shine .9s ease}
.trust-item:hover{transform:translateY(-7px);border-color:#99f6e4}
.trust-item i{font-size:1.65rem;color:var(--primary)}
.trust-item strong{display:block;color:var(--ink);margin-top:.55rem}
.trust-item span{display:block;color:var(--muted);font-size:.92rem;margin-top:.18rem}

/* Problem/Solution */
.problem-wrap{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:1.2rem;
}
.problem-card,.solution-card{
    border-radius:32px;
    padding:1.25rem;
    border:1px solid var(--border);
    background:white;
    box-shadow:0 16px 36px rgba(15,23,42,.065);
}
.problem-card{
    background:linear-gradient(180deg,#fff,#fef2f2);
}
.solution-card{
    background:linear-gradient(180deg,#fff,#ecfeff);
}
.problem-card h3,.solution-card h3{color:var(--ink);font-size:1.45rem;margin:.3rem 0 1rem}
.problem-list{
    display:grid;
    gap:.85rem;
}
.problem-list div{
    display:flex;
    gap:.7rem;
    color:var(--text);
    line-height:1.55;
}
.problem-list i{font-size:1.2rem;margin-top:.1rem}
.problem-card i{color:#ef4444}
.solution-card i{color:var(--primary)}

/* Features */
.feature-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:1rem;
}
.feature-card{
    background:white;
    border:1px solid var(--border);
    border-radius:30px;
    padding:1.15rem;
    box-shadow:0 15px 36px rgba(15,23,42,.06);
    transition:.28s ease;
}
.feature-card:hover{
    transform:translateY(-10px);
    box-shadow:0 24px 52px rgba(15,23,42,.12);
    border-color:#99f6e4;
}
.feature-icon{
    width:62px;height:62px;
    border-radius:22px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,#ecfeff,#eff6ff);
    color:var(--primary);
    font-size:1.75rem;
    margin-bottom:1rem;
}
.feature-card h3{color:var(--ink);margin:.3rem 0 .45rem}
.feature-card p{color:var(--muted);line-height:1.65;margin:0}

/* Split showcase */
.showcase{
    display:grid;
    grid-template-columns:.95fr 1.05fr;
    gap:2.4rem;
    align-items:center;
}
.showcase img{
    width:100%;
    height:400px;
    object-fit:cover;
    border-radius:36px;
    box-shadow:var(--shadow);
}
.showcase-copy h2{
    color:var(--ink);
    font-size:2.15rem;
    line-height:1.1;
    letter-spacing:-.04em;
    margin:.6rem 0 1rem;
}
.showcase-copy p{color:var(--muted);line-height:1.75;font-size:1.05rem}
.checks{
    display:grid;
    gap:.85rem;
    margin-top:.8rem;
}
.checks div{
    display:flex;
    align-items:flex-start;
    gap:.75rem;
    background:white;
    border:1px solid var(--border);
    border-radius:20px;
    padding:.95rem;
    transition:.25s ease;
}
.checks div:hover{transform:translateX(8px);border-color:#99f6e4}
.checks i{color:var(--primary);font-size:1.22rem;margin-top:.1rem}
.checks strong{color:var(--ink)}
.checks span{color:var(--muted);font-size:.92rem;display:block;margin-top:.15rem}

/* Users */
.users-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:1rem;
}
.user-card{
    text-align:center;
    background:linear-gradient(180deg,#ffffff,#f8fafc);
    border:1px solid var(--border);
    border-radius:30px;
    padding:1.15rem;
    transition:.28s ease;
}
.user-card:hover{
    transform:translateY(-9px) scale(1.015);
    border-color:#99f6e4;
    box-shadow:0 22px 45px rgba(15,23,42,.1);
}
.user-card i{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:64px;height:64px;
    border-radius:22px;
    background:var(--soft);
    color:var(--primary);
    font-size:1.9rem;
    margin-bottom:.9rem;
}
.user-card h3{color:var(--ink);margin:.2rem 0}
.user-card p{color:var(--muted);line-height:1.55;font-size:.93rem;margin:.35rem 0 0}

/* How it works */
.steps{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:1rem;
}
.step-card{
    position:relative;
    padding:1.2rem;
    border-radius:30px;
    background:white;
    border:1px solid var(--border);
    box-shadow:0 15px 36px rgba(15,23,42,.06);
    transition:.28s ease;
}
.step-card:hover{transform:translateY(-9px);border-color:#99f6e4}
.step-no{
    width:44px;height:44px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,var(--primary),var(--blue));
    color:white;
    font-weight:950;
    margin-bottom:1rem;
}
.step-card h3{color:var(--ink);margin:.3rem 0}
.step-card p{color:var(--muted);line-height:1.62;margin:.4rem 0 0}

/* CTA */
.cta-pro{
    margin:2.6rem 0 1.5rem;
    position:relative;
    overflow:hidden;
    border-radius:38px;
    padding:2.25rem;
    background:linear-gradient(135deg,#0f766e,#2563eb);
    color:white;
    display:grid;
    grid-template-columns:1fr auto;
    gap:1.5rem;
    align-items:center;
    box-shadow:0 28px 70px rgba(15,118,110,.25);
}
.cta-pro:before{
    content:"";
    position:absolute;
    width:260px;height:260px;
    border-radius:50%;
    background:rgba(255,255,255,.16);
    top:-120px;right:-80px;
}
.cta-pro h2{font-size:2rem;line-height:1.1;margin:.2rem 0}
.cta-pro p{margin:.6rem 0 0;color:#dbeafe}
.cta-pro .hero-actions{margin:0;position:relative;z-index:2}
.cta-pro .btn-primary-pro{background:white;color:#0f766e}
.cta-pro .btn-secondary-pro{background:rgba(255,255,255,.12);color:white;border-color:rgba(255,255,255,.28)}
.cta-pro .btn-secondary-pro:hover{background:rgba(255,255,255,.2);color:white}

/* Mobile */
@media(max-width:1000px){
    .landing-hero,.showcase,.problem-wrap{grid-template-columns:1fr}
    .hero-copy h1{font-size:3rem}
    .hero-visual{min-height:480px}
    .hero-image-card{inset:0}
    .feature-grid,.steps{grid-template-columns:1fr}
    .trust-strip,.users-grid{grid-template-columns:repeat(2,1fr)}
    .cta-pro{grid-template-columns:1fr}
}
@media(max-width:620px){
    .landing-hero{min-height:auto;padding:2.5rem 0}
    .hero-copy h1{font-size:2.35rem}
    .hero-copy p{font-size:1rem}
    .hero-visual{min-height:380px}
    .hero-image-card img,.showcase img{height:380px}
    .doctor-card{width:245px;left:14px}
    .secure-bubble{display:none}
    .trust-strip,.users-grid{grid-template-columns:1fr}
    .section-title h2,.showcase-copy h2{font-size:2rem}
    .cta-pro{padding:2rem}
}

@media (prefers-reduced-motion: reduce){
    *{animation:none!important;transition:none!important;scroll-behavior:auto!important}
}
</style>

<section class="landing-hero">
    <div class="hero-copy reveal-left">
        <span class="hero-kicker"><i class="bi bi-hospital"></i> Modern Healthcare Records</span>
        <h1>Secure patient records.</h1>
        <p>A clean and secure e-health platform that helps healthcare teams organize records, protect patient information, and deliver faster care.</p>

        <div class="hero-actions">
            <a class="btn-primary-pro" href="<?= e(siteUrl('login.php')) ?>"><i class="bi bi-box-arrow-in-right"></i> Login to System</a>
            <a class="btn-secondary-pro" href="<?= e(siteUrl('register.php')) ?>"><i class="bi bi-person-plus"></i> Create Account</a>
        </div>

        <div class="hero-points">
            <span><i class="bi bi-check-circle-fill"></i> Secure access</span>
            <span><i class="bi bi-check-circle-fill"></i> Faster workflow</span>
            <span><i class="bi bi-check-circle-fill"></i> Better record control</span>
        </div>
    </div>

    <div class="hero-visual reveal-right">
        <div class="hero-image-card">
            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1500&q=90" alt="Healthcare worker using a digital medical record system">
        </div>
        <div class="secure-bubble bouncy-small">
            <i class="bi bi-shield-check"></i>
            <strong>Protected</strong>
            <span>Access is limited to the right users.</span>
        </div>
        <div class="doctor-card bouncy">
            <div class="top">
                <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=300&q=90" alt="Doctor profile">
                <div>
                    <strong>Patient Record</strong>
                    <small>Ready for secure review</small>
                </div>
            </div>
            <div class="bar"><span></span></div>
        </div>
    </div>
</section>

<section class="trust-strip">
    <div class="trust-item reveal"><i class="bi bi-lock"></i><strong>Privacy focused</strong><span>Designed to protect patient data.</span></div>
    <div class="trust-item reveal"><i class="bi bi-speedometer2"></i><strong>Fast access</strong><span>Records are easier to find and update.</span></div>
    <div class="trust-item reveal"><i class="bi bi-person-check"></i><strong>Verified users</strong><span>Clinical staff access is controlled.</span></div>
    <div class="trust-item reveal"><i class="bi bi-phone"></i><strong>Responsive</strong><span>Works across laptop, tablet, and mobile.</span></div>
</section>

<section class="section-pro">
    <div class="section-title reveal">
        <span class="eyebrow">The challenge</span>
        <h2>Records made safer.</h2>
        <p>A cleaner workflow for safer health records.</p>
    </div>
    <div class="problem-wrap">
        <div class="problem-card reveal-left">
            <h3>Common record problems</h3>
            <div class="problem-list">
                <div><i class="bi bi-x-circle-fill"></i><span>Paper files can be misplaced, damaged, or difficult to retrieve quickly.</span></div>
                <div><i class="bi bi-x-circle-fill"></i><span>Uncontrolled access can expose sensitive patient information.</span></div>
                <div><i class="bi bi-x-circle-fill"></i><span>Clinical updates become harder to track when records are not centralized.</span></div>
            </div>
        </div>
        <div class="solution-card reveal-right">
            <h3>How this system helps</h3>
            <div class="problem-list">
                <div><i class="bi bi-check-circle-fill"></i><span>Patient records are organized digitally for faster retrieval.</span></div>
                <div><i class="bi bi-check-circle-fill"></i><span>User access is separated based on healthcare responsibilities.</span></div>
                <div><i class="bi bi-check-circle-fill"></i><span>Important activities can be monitored for accountability.</span></div>
            </div>
        </div>
    </div>
</section>

<section class="section-pro">
    <div class="section-title reveal">
        <span class="eyebrow">Core benefits</span>
        <h2>Built for care teams.</h2>
        <p>Simple, secure, and organized for healthcare work.</p>
    </div>

    <div class="feature-grid">
        <div class="feature-card reveal">
            <div class="feature-icon"><i class="bi bi-folder2-open"></i></div>
            <h3>Digital record management</h3>
            <p>Store and access patient information in a more organized and reliable way.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon"><i class="bi bi-shield-lock"></i></div>
            <h3>Controlled access</h3>
            <p>Users only access features and records that match their responsibilities.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon"><i class="bi bi-person-vcard"></i></div>
            <h3>Staff verification</h3>
            <p>Clinical users are checked before they can access sensitive work areas.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon"><i class="bi bi-clipboard2-pulse"></i></div>
            <h3>Clinical updates</h3>
            <p>Doctors and nurses can update patient records through a guided workflow.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon"><i class="bi bi-activity"></i></div>
            <h3>Activity tracking</h3>
            <p>Important system actions can be reviewed for transparency and accountability.</p>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon"><i class="bi bi-layout-text-window"></i></div>
            <h3>Clean user experience</h3>
            <p>Dashboards are clear and focused so users can work with less confusion.</p>
        </div>
    </div>
</section>

<section class="section-pro showcase">
    <img class="reveal-left" src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?auto=format&fit=crop&w=1500&q=90" alt="Healthcare staff reviewing medical information">
    <div class="showcase-copy reveal-right">
        <span class="hero-kicker"><i class="bi bi-heart-pulse"></i> Care Workflow</span>
        <h2>Built for care roles.</h2>
        <p>The platform provides a clear journey from user registration to patient assignment and record updates, while keeping sensitive information protected.</p>
        <div class="checks">
            <div><i class="bi bi-check-circle-fill"></i><span><strong>Administrators</strong><span>Manage accounts, approvals, assignments, and monitoring.</span></span></div>
            <div><i class="bi bi-check-circle-fill"></i><span><strong>Clinical staff</strong><span>Access assigned patient information and update care details.</span></span></div>
            <div><i class="bi bi-check-circle-fill"></i><span><strong>Patients</strong><span>View their personal health records through a simple account.</span></span></div>
        </div>
    </div>
</section>

<section class="section-pro">
    <div class="section-title reveal">
        <span class="eyebrow">User roles</span>
        <h2>Clear user access.</h2>
        <p>Each role has a focused experience to reduce confusion and protect sensitive information.</p>
    </div>
    <div class="users-grid">
        <div class="user-card reveal"><i class="bi bi-person-gear"></i><h3>Admin</h3><p>Controls users, approvals, assignments, and monitoring.</p></div>
        <div class="user-card reveal"><i class="bi bi-person-vcard"></i><h3>Doctor</h3><p>Reviews patient history and updates clinical treatment details.</p></div>
        <div class="user-card reveal"><i class="bi bi-heart-pulse"></i><h3>Nurse</h3><p>Records vital signs and nursing observations for assigned patients.</p></div>
        <div class="user-card reveal"><i class="bi bi-person"></i><h3>Patient</h3><p>Accesses personal health records and profile information.</p></div>
    </div>
</section>

<section class="section-pro">
    <div class="section-title reveal">
        <span class="eyebrow">How it works</span>
        <h2>How it works.</h2>
        <p>A simple journey for every user.</p>
    </div>
    <div class="steps">
        <div class="step-card reveal">
            <div class="step-no">1</div>
            <h3>Create an account</h3>
            <p>Users register with basic information and access the system based on their role.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-no">2</div>
            <h3>Verify and assign</h3>
            <p>Clinical users are reviewed, and patients are connected to the right care team.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-no">3</div>
            <h3>Manage records</h3>
            <p>Patient information is viewed and updated through secure role-based dashboards.</p>
        </div>
    </div>
</section>

<section class="section-pro showcase">
    <div class="showcase-copy reveal-left">
        <span class="hero-kicker"><i class="bi bi-shield-check"></i> Security First</span>
        <h2>Security first.</h2>
        <p>The landing page explains security in simple user-focused language, while the system keeps the real protection logic inside the application.</p>
        <div class="checks">
            <div><i class="bi bi-check-circle-fill"></i><span><strong>Private health data</strong><span>Only authorized users can access protected areas.</span></span></div>
            <div><i class="bi bi-check-circle-fill"></i><span><strong>Controlled clinical access</strong><span>Staff access follows healthcare responsibility.</span></span></div>
            <div><i class="bi bi-check-circle-fill"></i><span><strong>Better accountability</strong><span>Important actions can be monitored by the administrator.</span></span></div>
        </div>
    </div>
    <img class="reveal-right" src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=1500&q=90" alt="Medical professionals in a hospital environment">
</section>

<section class="cta-pro reveal">
    <div>
        <span class="eyebrow" style="color:#ccfbf1">Get started</span>
        <h2>Ready to get started?</h2>
        <p>Login to continue or create a new account to access the system.</p>
    </div>
    <div class="hero-actions">
        <a class="btn-primary-pro" href="<?= e(siteUrl('login.php')) ?>"><i class="bi bi-box-arrow-in-right"></i> Login</a>
        <a class="btn-secondary-pro" href="<?= e(siteUrl('register.php')) ?>"><i class="bi bi-person-plus"></i> Register</a>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const items = document.querySelectorAll(".reveal,.reveal-left,.reveal-right");
    const observer = new IntersectionObserver((entries)=>{
        entries.forEach((entry)=>{
            if(entry.isIntersecting){
                entry.target.classList.add("visible");
                observer.unobserve(entry.target);
            }
        });
    }, {threshold:0.14});
    items.forEach((item, index)=>{
        item.style.animationDelay = ((index % 5) * 0.07) + "s";
        observer.observe(item);
    });
});
</script>
<?php renderFooter(); ?>
