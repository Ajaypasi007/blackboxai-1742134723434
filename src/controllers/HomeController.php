<?php
/**
 * Home Controller
 * Handles public-facing pages
 */
class HomeController extends BaseController {
    /**
     * Display home page
     */
    public function index() {
        $this->render('home', [
            'pageTitle' => APP_NAME . ' - Social Media Management Made Easy'
        ]);
    }
    
    /**
     * Display features page
     */
    public function features() {
        $this->render('features', [
            'pageTitle' => 'Features - ' . APP_NAME,
            'features' => [
                [
                    'title' => 'Cross-Platform Posting',
                    'description' => 'Post to multiple social media platforms from one central dashboard.',
                    'icon' => 'fas fa-share-alt'
                ],
                [
                    'title' => 'Smart Scheduling',
                    'description' => 'Schedule posts for optimal engagement times using AI-powered recommendations.',
                    'icon' => 'fas fa-clock'
                ],
                [
                    'title' => 'Advanced Analytics',
                    'description' => 'Get detailed insights into your social media performance across all platforms.',
                    'icon' => 'fas fa-chart-line'
                ],
                [
                    'title' => 'Team Collaboration',
                    'description' => 'Work together with your team using roles, approvals, and shared calendars.',
                    'icon' => 'fas fa-users'
                ]
            ]
        ]);
    }
    
    /**
     * Display pricing page
     */
    public function pricing() {
        global $SUBSCRIPTION_PLANS;
        
        $this->render('pricing', [
            'pageTitle' => 'Pricing - ' . APP_NAME,
            'plans' => $SUBSCRIPTION_PLANS
        ]);
    }
    
    /**
     * Display about page
     */
    public function about() {
        $this->render('about', [
            'pageTitle' => 'About Us - ' . APP_NAME
        ]);
    }
    
    /**
     * Display contact page
     */
    public function contact() {
        $this->render('contact', [
            'pageTitle' => 'Contact Us - ' . APP_NAME
        ]);
    }
    
    /**
     * Handle contact form submission
     */
    public function submitContact() {
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/contact');
        }
        
        $data = [
            'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'subject' => filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING),
            'message' => filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING)
        ];
        
        // Validate input
        $errors = $this->validate($data, [
            'name' => 'required|max:100',
            'email' => 'required|email',
            'subject' => 'required|max:200',
            'message' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->render('contact', [
                'pageTitle' => 'Contact Us - ' . APP_NAME,
                'errors' => $errors,
                'data' => $data
            ]);
            return;
        }
        
        try {
            // Save message
            $stmt = $this->db->prepare('
                INSERT INTO contact_messages (
                    name,
                    email,
                    subject,
                    message,
                    created_at
                ) VALUES (?, ?, ?, ?, NOW())
            ');
            
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['subject'],
                $data['message']
            ]);
            
            // Send notification email to admin
            $adminEmail = 'admin@' . $_SERVER['HTTP_HOST'];
            $subject = 'New Contact Form Submission';
            $body = "
                <h2>New Contact Form Submission</h2>
                <p><strong>Name:</strong> {$data['name']}</p>
                <p><strong>Email:</strong> {$data['email']}</p>
                <p><strong>Subject:</strong> {$data['subject']}</p>
                <p><strong>Message:</strong></p>
                <p>{$data['message']}</p>
            ";
            
            sendEmail($adminEmail, $subject, $body);
            
            // Send confirmation email to user
            $subject = 'Thank you for contacting ' . APP_NAME;
            $body = "
                <h2>Thank you for contacting us!</h2>
                <p>Dear {$data['name']},</p>
                <p>We have received your message and will get back to you as soon as possible.</p>
                <p>Best regards,<br>The " . APP_NAME . " Team</p>
            ";
            
            sendEmail($data['email'], $subject, $body);
            
            $this->setFlash('success', 'Your message has been sent successfully!');
            $this->redirect('/contact');
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Failed to send message. Please try again.');
            $this->redirect('/contact');
        }
    }
    
    /**
     * Display blog page
     */
    public function blog() {
        try {
            // Get blog posts
            $stmt = $this->db->prepare('
                SELECT 
                    p.*,
                    u.first_name,
                    u.last_name
                FROM blog_posts p
                JOIN users u ON p.author_id = u.id
                WHERE p.status = "published"
                ORDER BY p.published_at DESC
                LIMIT 10
            ');
            $stmt->execute();
            
            $this->render('blog', [
                'pageTitle' => 'Blog - ' . APP_NAME,
                'posts' => $stmt->fetchAll()
            ]);
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->setFlash('error', 'Failed to load blog posts');
            $this->redirect('/');
        }
    }
    
    /**
     * Display help page
     */
    public function help() {
        $this->render('help', [
            'pageTitle' => 'Help Center - ' . APP_NAME
        ]);
    }
    
    /**
     * Display terms page
     */
    public function terms() {
        $this->render('terms', [
            'pageTitle' => 'Terms of Service - ' . APP_NAME
        ]);
    }
    
    /**
     * Display privacy page
     */
    public function privacy() {
        $this->render('privacy', [
            'pageTitle' => 'Privacy Policy - ' . APP_NAME
        ]);
    }
}
?>
