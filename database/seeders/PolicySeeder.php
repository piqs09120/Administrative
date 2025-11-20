<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Policy;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Policy::updateOrCreate(
            ['slug' => 'terms'],
            [
                'title' => 'General Terms & Conditions',
                'content' => '<div class="prose max-w-none">
                    <h2>General Terms & Conditions</h2>
                    <p>Welcome to Soliera Hotel. By accessing and using our services, you agree to be bound by the following terms and conditions.</p>
                    
                    <h3>1. Acceptance of Terms</h3>
                    <p>By using our services, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>
                    
                    <h3>2. Services</h3>
                    <p>Soliera Hotel provides accommodation, dining, and related hospitality services. All services are subject to availability and applicable laws.</p>
                    
                    <h3>3. Reservations and Cancellations</h3>
                    <p>Reservations are subject to our cancellation policy. Please review cancellation terms at the time of booking.</p>
                    
                    <h3>4. Guest Responsibilities</h3>
                    <p>Guests are responsible for their conduct and any damages to hotel property during their stay.</p>
                    
                    <h3>5. Limitation of Liability</h3>
                    <p>Soliera Hotel shall not be liable for any indirect, incidental, or consequential damages arising from the use of our services.</p>
                    
                    <h3>6. Governing Law</h3>
                    <p>These terms are governed by the laws of the Philippines.</p>
                    
                    <p><strong>Last Updated:</strong> November 10, 2025</p>
                </div>',
                'version' => 1,
                'is_active' => true
            ]
        );

        Policy::updateOrCreate(
            ['slug' => 'privacy'],
            [
                'title' => 'Privacy Policy',
                'content' => '<div class="prose max-w-none">
                    <h2>Privacy Policy</h2>
                    <p>Soliera Hotel is committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information.</p>
                    
                    <h3>1. Information We Collect</h3>
                    <p>We collect information that you provide directly to us, including:</p>
                    <ul>
                        <li>Name and contact information</li>
                        <li>Payment information</li>
                        <li>Identification documents</li>
                        <li>Preferences and special requests</li>
                    </ul>
                    
                    <h3>2. How We Use Your Information</h3>
                    <p>We use your information to:</p>
                    <ul>
                        <li>Process reservations and bookings</li>
                        <li>Provide customer service</li>
                        <li>Send important updates and communications</li>
                        <li>Comply with legal obligations</li>
                    </ul>
                    
                    <h3>3. Information Sharing</h3>
                    <p>We do not sell your personal information. We may share information with:</p>
                    <ul>
                        <li>Service providers who assist in our operations</li>
                        <li>Legal authorities when required by law</li>
                    </ul>
                    
                    <h3>4. Data Security</h3>
                    <p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, or disclosure.</p>
                    
                    <h3>5. Your Rights</h3>
                    <p>You have the right to access, correct, or delete your personal information. Please contact us to exercise these rights.</p>
                    
                    <h3>6. Contact Us</h3>
                    <p>If you have questions about this Privacy Policy, please contact our Data Protection Officer.</p>
                    
                    <p><strong>Last Updated:</strong> November 10, 2025</p>
                </div>',
                'version' => 1,
                'is_active' => true
            ]
        );
    }
}


