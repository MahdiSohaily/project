<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

class Bill
{
    public function getBill($billNumber)
    {
        $sql = "SELECT bill.*, customer.name, customer.family, customer.address, customer.phone FROM factor.bill
        INNER JOIN callcenter.customer ON bill.customer_id = customer.id
        WHERE bill.id = :billNumber ORDER BY bill_number DESC LIMIT 1";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':billNumber', $billNumber);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function getBillItems($billNumber)
    {
        $sql = "SELECT * FROM factor.bill_details WHERE bill_id = :bill_id ORDER BY id DESC LIMIT 1";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':bill_id', $billNumber);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function getCustomer($customerId)
    {
        $sql = "SELECT * FROM callcenter.customer WHERE id = :id ORDER BY id DESC LIMIT 1";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':id', $customerId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
}
