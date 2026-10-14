<?php
require_once __DIR__ . '/inc/bootstrap.php';

$pageTitle = t('About Us – Zam Zam Tour & Travels');
require __DIR__ . '/inc/header.php';
?>

<main class="wrap about-page">

    <section class="about-hero">
        <h1 class="h"><?= e(t('About Us')) ?></h1>

        <h2><?= e(t('Your Trusted Partner for Hajj, Umrah & Ziyarat')) ?></h2>

        <p>
            <?= e(t('Welcome to')) ?> <strong>Zam Zam Tour & Travels</strong><?= e(t(', your trusted travel partner for Hajj, Umrah and Ziyarat. We aim to make your spiritual journey comfortable, convenient and memorable through reliable travel arrangements and dedicated assistance.')) ?>
        </p>

        <p>
            <?= e(t('From flight tickets and visa assistance to hotel accommodation, transportation and Ziyarat tours, we help you prepare for your journey to the holy cities of Makkah and Madinah.')) ?>
        </p>

        <a class="btn green" href="packages.php">
            <?= e(t('Explore Our Packages')) ?>
        </a>
    </section>

    <section class="about-section">
        <h2><?= e(t('Our Services')) ?></h2>

        <div class="about-grid">

            <article class="about-card">
                <h3><?= e(t('Hajj Packages')) ?></h3>
                <p>
                    <?= e(t('Travel arrangements and assistance for your sacred Hajj journey.')) ?>
                </p>
            </article>

            <article class="about-card">
                <h3><?= e(t('Umrah Packages')) ?></h3>
                <p>
                    <?= e(t('Package options designed for different budgets, travel dates and accommodation preferences.')) ?>
                </p>
            </article>

            <article class="about-card">
                <h3><?= e(t('Ziyarat Tours')) ?></h3>
                <p>
                    <?= e(t('Explore important Islamic historical and religious sites in Makkah and Madinah.')) ?>
                </p>
            </article>

            <article class="about-card">
                <h3><?= e(t('Travel Assistance')) ?></h3>
                <p>
                    <?= e(t('Assistance with flight tickets, visa documentation, hotel bookings and transportation arrangements.')) ?>
                </p>
            </article>

        </div>
    </section>

    <section class="about-section">
        <h2><?= e(t('Our Umrah Packages')) ?></h2>

        <p>
            <?= e(t('Choose from our available Economy, T.I.P., Golden and Premium package options. Hotel distances, facilities and prices depend on the selected package and travel dates.')) ?>
        </p>

        <a class="btn green" href="packages.php">
            <?= e(t('View All Packages')) ?>
        </a>
    </section>

    <section class="about-section">
        <h2><?= e(t('Why Choose Us?')) ?></h2>

        <ul class="about-list">
            <li><?= e(t('Package options for different budgets')) ?></li>
            <li><?= e(t('Assistance with travel planning and documentation')) ?></li>
            <li><?= e(t('Hotel accommodation and transportation arrangements')) ?></li>
            <li><?= e(t('Support throughout the booking process')) ?></li>
            <li><?= e(t('Focus on comfortable and well-organized travel')) ?></li>
        </ul>
    </section>

    <section class="about-mission">
        <h2><?= e(t('Our Mission')) ?></h2>

        <p>
            <?= e(t('Our mission is to help pilgrims prepare for a smooth, comfortable and spiritually fulfilling journey to the holy cities of Makkah and Madinah.')) ?>
        </p>

        <h3><?= e(t('Plan Your Sacred Journey With Us')) ?></h3>

        <p>
            <?= e(t('Contact Zam Zam Tour & Travels to learn more about our Hajj, Umrah and Ziyarat packages.')) ?>
        </p>

        <p>
            <strong><?= e(t('Phone:')) ?></strong>
            <a href="tel:+917439805126">+91 74398 05126</a>
        </p>

        <p>
            <strong><?= e(t('Email:')) ?></strong>
            <a href="mailto:zamzamtoursandtravels5951@gmail.com">
                zamzamtoursandtravels5951@gmail.com
            </a>
        </p>

        <a class="btn green" href="packages.php">
            <?= e(t('View All Packages')) ?>
        </a>
    </section>

</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
</body>

</html>