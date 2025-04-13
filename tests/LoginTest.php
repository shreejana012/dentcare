<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $mockConn;
    private $mockStmt;
    private $mockResult;

    protected function setUp(): void
    {
        // Clear existing session
        $_SESSION = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        // Only define test mode if not already defined
        if (!defined('PHPUNIT_TEST')) {
            define('PHPUNIT_TEST', true);
        }

        // Create proper mock objects
        $this->mockResult = $this->getMockBuilder(stdClass::class)
            ->addMethods(['fetch_assoc'])
            ->getMock();
        
        $this->mockStmt = $this->getMockBuilder(stdClass::class)
            ->addMethods(['bind_param', 'execute', 'get_result'])
            ->getMock();
            
        $this->mockConn = $this->getMockBuilder(mysqli::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['prepare'])
            ->getMock();

        $GLOBALS['conn'] = $this->mockConn;
    }

    protected function tearDown(): void
    {
        // Clean up
        unset($GLOBALS['conn']);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function testSuccessfulLogin()
    {
        $_POST['email'] = 'test@example.com';
        $_POST['password'] = 'password123';
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);

        // Configure mocks
        $this->mockResult->method('fetch_assoc')
            ->willReturn([
                'id' => 1,
                'email' => 'test@example.com',
                'username' => 'TestUser',
                'password' => $hashedPassword
            ]);
        $this->mockResult->num_rows = 1;

        $this->mockStmt->method('get_result')
            ->willReturn($this->mockResult);

        $this->mockConn->method('prepare')
            ->willReturn($this->mockStmt);

        // Run test
        require __DIR__ . '/../auth/login_logic.php';

        // Assertions
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('test@example.com', $_SESSION['email']);
    }

    public function testLoginWithWrongPassword()
    {
        $_POST['email'] = 'test@example.com';
        $_POST['password'] = 'wrongpassword';
        $hashedPassword = password_hash('correctpassword', PASSWORD_DEFAULT);

        // Configure mocks
        $this->mockResult->method('fetch_assoc')
            ->willReturn([
                'id' => 1,
                'email' => 'test@example.com',
                'username' => 'TestUser',
                'password' => $hashedPassword
            ]);
        $this->mockResult->num_rows = 1;

        $this->mockStmt->method('get_result')
            ->willReturn($this->mockResult);

        $this->mockConn->method('prepare')
            ->willReturn($this->mockStmt);

        // Run test
        require __DIR__ . '/../auth/login_logic.php';

        // Assertions
        $this->assertEquals("Invalid password. Please try again.", $_SESSION['login_error']);
    }

    public function testLoginWithNonexistentEmail()
    {
        $_POST['email'] = 'nonexistent@example.com';
        $_POST['password'] = 'irrelevant';

        // Configure mocks
        $this->mockResult->num_rows = 0;
        $this->mockResult->method('fetch_assoc')
            ->willReturn(null);

        $this->mockStmt->method('get_result')
            ->willReturn($this->mockResult);

        $this->mockConn->method('prepare')
            ->willReturn($this->mockStmt);

        // Run test
        require __DIR__ . '/../auth/login_logic.php';

        // Assertions
        $this->assertEquals("No user found with this email.", $_SESSION['login_error']);
    }
}