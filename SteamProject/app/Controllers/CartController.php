<?php
require_once BASE_PATH . '/app/Helpers/store_commerce.php';

class CartController
{
    public function sync(): void
    {
        $payload = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($payload) || !is_array($payload['items'] ?? null)) {
            $this->respond(['ok' => false, 'message' => 'Dữ liệu giỏ hàng không hợp lệ.'], 400);
            return;
        }

        try {
            $cart = syncStoreCart(
                session_id(),
                $this->currentCustomerName(),
                $payload['items']
            );
            $this->respond(['ok' => true, 'cart' => $cart]);
        } catch (InvalidArgumentException | DomainException $error) {
            $this->respond(['ok' => false, 'message' => $error->getMessage()], 422);
        } catch (PDOException $error) {
            error_log('Unable to sync store cart: ' . $error->getMessage());
            $this->respond(['ok' => false, 'message' => 'Không thể lưu giỏ hàng lúc này.'], 500);
        }
    }

    public function checkout(): void
    {
        if (empty($_SESSION['user'])) {
            $this->respond(['ok' => false, 'message' => 'Vui lòng đăng nhập trước khi thanh toán.'], 401);
            return;
        }

        try {
            $orderId = createStoreOrderFromCart(session_id(), $this->currentCustomerName());
            $this->respond(['ok' => true, 'order_id' => $orderId]);
        } catch (DomainException $error) {
            $this->respond(['ok' => false, 'message' => $error->getMessage()], 409);
        } catch (PDOException $error) {
            error_log('Unable to create store order: ' . $error->getMessage());
            $this->respond(['ok' => false, 'message' => 'Không thể tạo đơn hàng lúc này.'], 500);
        }
    }

    private function currentCustomerName(): string
    {
        $user = $_SESSION['user'] ?? null;
        if (is_array($user)) {
            return trim((string) ($user['name'] ?? $user['username'] ?? 'Guest')) ?: 'Guest';
        }
        return trim((string) $user) ?: 'Guest';
    }

    private function respond(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
