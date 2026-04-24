"""
Bootstrap script for training Vanna on the GroceMate schema and business rules.

This script keeps the initial training material close to the project so you can
extend it whenever the database or business logic changes.
"""

from __future__ import annotations

from vanna_adapter import VannaAdapter


def documentation_entries() -> list[str]:
    """
    Project-specific documentation snippets for Vanna.

    These are not SQL statements. They are plain-English rules that help Vanna
    map GroceMate terminology to the right schema and business meaning.
    """

    return [
        "GroceMate is a grocery and business management platform with inventory, POS, suppliers, purchases, and ecommerce modules.",
        "Low stock means a stock row where reorder_level > 0 and quantity <= reorder_level, or reorder_level <= 0 and quantity <= 0.",
        "The stock table uses product_id as the primary key and stores quantity and reorder_level.",
        "Products are stored in the products table and relate to stock through products.id = stock.product_id.",
        "Purchase items are stored in purchase_items and relate to purchases through purchase_items.purchase_id = purchases.id.",
        "Expiry analysis should use purchase_items.expiry_date when it is not null.",
        "Suppliers are stored in suppliers and purchases.supplier_id points to suppliers.id.",
        "Businesses are stored in businesses and many inventory records are business-specific.",
    ]


def sql_examples() -> list[str]:
    """
    Curated SQL examples that teach Vanna the kind of answers we want.
    """

    return [
        """
        SELECT p.name, s.quantity, s.reorder_level
        FROM stock s
        INNER JOIN products p ON p.id = s.product_id
        WHERE (
            (s.reorder_level > 0 AND s.quantity <= s.reorder_level)
            OR (s.reorder_level <= 0 AND s.quantity <= 0)
        )
        ORDER BY s.quantity ASC
        LIMIT 20
        """,
        """
        SELECT pi.product_name, pi.expiry_date, su.name AS supplier_name
        FROM purchase_items pi
        INNER JOIN purchases pu ON pu.id = pi.purchase_id
        INNER JOIN suppliers su ON su.id = pu.supplier_id
        WHERE pi.expiry_date IS NOT NULL
          AND pi.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ORDER BY pi.expiry_date ASC
        LIMIT 20
        """,
        """
        SELECT COUNT(*) AS supplier_count
        FROM suppliers
        """,
        """
        SELECT DATE(purchase_date) AS purchase_day, SUM(total_cost) AS total_purchase_amount
        FROM purchases
        GROUP BY DATE(purchase_date)
        ORDER BY purchase_day DESC
        LIMIT 30
        """,
    ]


def ddl_examples() -> list[str]:
    """
    Minimal DDL hints that reinforce the most important tables.
    """

    return [
        "CREATE TABLE products (id BIGINT PRIMARY KEY, business_id BIGINT, category_id BIGINT, brand_id BIGINT, name VARCHAR(255), unit VARCHAR(50), selling_price DECIMAL(10,2), is_active BOOLEAN, is_listed BOOLEAN);",
        "CREATE TABLE stock (product_id BIGINT PRIMARY KEY, quantity DECIMAL(12,3), reorder_level DECIMAL(12,3));",
        "CREATE TABLE suppliers (id BIGINT PRIMARY KEY, name VARCHAR(255));",
        "CREATE TABLE purchases (id BIGINT PRIMARY KEY, business_id BIGINT, supplier_id BIGINT, purchase_date DATE, invoice_no VARCHAR(100), total_cost DECIMAL(12,2));",
        "CREATE TABLE purchase_items (id BIGINT PRIMARY KEY, purchase_id BIGINT, product_id BIGINT, product_name VARCHAR(255), qty DECIMAL(12,3), unit_cost DECIMAL(12,2), line_total DECIMAL(12,2), expiry_date DATE);",
    ]


def main() -> None:
    """
    Train the Vanna model with GroceMate-specific context.
    """

    adapter = VannaAdapter()

    if not adapter.is_configured():
        raise SystemExit(
            "Vanna is not configured yet. Fill in python_services/vanna_admin_chatbot/.env before training."
        )

    client = adapter._build_client()

    # Documentation training teaches domain language and business meaning.
    for entry in documentation_entries():
        client.train(documentation=entry)
        print(f"Trained documentation entry: {entry}")

    # DDL training gives Vanna a stronger picture of the schema shape.
    for ddl in ddl_examples():
        client.train(ddl=ddl)
        print(f"Trained DDL entry: {ddl.splitlines()[0]}")

    # SQL training teaches concrete query patterns you care about.
    for sql in sql_examples():
        client.train(sql=sql)
        print("Trained SQL example.")

    print("GroceMate Vanna training bootstrap completed.")


if __name__ == "__main__":
    main()
