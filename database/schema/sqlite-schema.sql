CREATE TABLE IF NOT EXISTS "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);
CREATE TABLE IF NOT EXISTS "cache" ("key" varchar not null primary key, "value" text not null, "expiration" integer not null);
CREATE TABLE IF NOT EXISTS "cache_locks" ("key" varchar not null primary key, "owner" varchar not null, "expiration" integer not null);
CREATE TABLE IF NOT EXISTS "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" text not null, "queue" text not null, "payload" longtext not null, "exception" longtext not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs" ("uuid");
CREATE TABLE IF NOT EXISTS "job_batches" ("id" varchar not null primary key, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" longtext not null, "options" mediumtext, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer);
CREATE TABLE IF NOT EXISTS "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" longtext not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);
CREATE INDEX "jobs_queue_index" on "jobs" ("queue");
CREATE TABLE IF NOT EXISTS "password_reset_tokens" ("email" varchar not null primary key, "token" varchar not null, "created_at" datetime);
CREATE TABLE IF NOT EXISTS "sessions" ("id" varchar not null primary key, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" longtext not null, "last_activity" integer not null);
CREATE INDEX "sessions_user_id_index" on "sessions" ("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions" ("last_activity");
CREATE TABLE IF NOT EXISTS "companies" ("id" integer primary key autoincrement not null, "is_active" tinyint(1) not null default '1', "name" varchar not null, "legal_name" varchar, "address" text, "phone_number" varchar, "email" varchar, "rccm" varchar, "nif" varchar, "owner_id" integer, "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);
CREATE TABLE IF NOT EXISTS "plans" ("id" integer primary key autoincrement not null, "name" varchar not null, "slug" varchar not null, "description" text, "price" integer not null default '0', "user_limit" integer not null default '1', "unlimited_users" tinyint(1) not null default '0', "features" text not null, "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "plans_slug_unique" on "plans" ("slug");
CREATE TABLE IF NOT EXISTS "permissions" ("id" integer primary key autoincrement not null, "company_id" integer, "name" varchar not null, "guard_name" varchar not null, "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "permissions_name_guard_name_unique" on "permissions" ("name", "guard_name");
CREATE TABLE IF NOT EXISTS "roles" ("id" integer primary key autoincrement not null, "company_id" integer, "name" varchar not null, "guard_name" varchar not null, "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "roles_name_guard_name_unique" on "roles" ("name", "guard_name");
CREATE TABLE IF NOT EXISTS "stores" ("id" integer primary key autoincrement not null, "company_id" integer not null, "name" varchar not null, "address" text, "city" varchar, "contact_phone" varchar, "is_active" tinyint(1) not null default '1', "is_country_branch" tinyint(1) not null default '0', "country_code" varchar, "country_name" varchar, "nif" varchar, "rccm" varchar, "business_permit" varchar, "tax_id" varchar, "email" varchar, "website" varchar, "postal_box" varchar, "invoice_header_image" varchar, "invoice_footer_image" varchar, "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);
CREATE INDEX "stores_company_id_index" on "stores" ("company_id");
CREATE TABLE IF NOT EXISTS "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "company_id" integer, "is_global_admin" tinyint(1) not null default '0', "store_id" integer, "last_login_at" datetime, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);
CREATE UNIQUE INDEX "users_email_unique" on "users" ("email");
CREATE INDEX "users_company_id_index" on "users" ("company_id");
CREATE TABLE IF NOT EXISTS "model_has_permissions" ("permission_id" integer not null, "model_type" varchar not null, "model_id" integer not null, "company_id" integer, "team_id" integer, primary key ("permission_id", "model_id", "model_type"));
CREATE INDEX "model_has_permissions_model_id_model_type_index" on "model_has_permissions" ("model_id", "model_type");
CREATE TABLE IF NOT EXISTS "model_has_roles" ("role_id" integer not null, "model_type" varchar not null, "model_id" integer not null, "company_id" integer, "team_id" integer, primary key ("role_id", "model_id", "model_type"));
CREATE INDEX "model_has_roles_model_id_model_type_index" on "model_has_roles" ("model_id", "model_type");
CREATE TABLE IF NOT EXISTS "role_has_permissions" ("permission_id" integer not null, "role_id" integer not null, primary key ("permission_id", "role_id"));
CREATE TABLE IF NOT EXISTS "media" ("id" integer primary key autoincrement not null, "model_type" varchar not null, "model_id" integer not null, "uuid" char(36), "collection_name" varchar not null, "name" varchar not null, "file_name" varchar not null, "mime_type" varchar, "disk" varchar not null, "conversions_disk" varchar, "size" integer not null, "manipulations" text not null, "custom_properties" text not null, "generated_conversions" text not null, "responsive_images" text not null, "order_column" integer, "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "media_uuid_unique" on "media" ("uuid");
CREATE INDEX "media_model_type_model_id_index" on "media" ("model_type", "model_id");
CREATE TABLE IF NOT EXISTS "subscriptions" ("id" integer primary key autoincrement not null, "company_id" integer not null, "plan_id" integer not null, "starts_at" datetime not null, "ends_at" datetime not null, "status" varchar not null default 'active', "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "taxes" ("id" integer primary key autoincrement not null, "company_id" integer not null, "name" varchar not null, "rate" numeric not null, "is_default" tinyint(1) not null default '0', "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "units" ("id" integer primary key autoincrement not null, "company_id" integer not null, "name" varchar not null, "symbol" varchar not null, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "categories" ("id" integer primary key autoincrement not null, "company_id" integer not null, "parent_id" integer, "name" varchar not null, "slug" varchar not null, "description" text, "created_at" datetime, "updated_at" datetime);
CREATE INDEX "categories_company_id_index" on "categories" ("company_id");
CREATE TABLE IF NOT EXISTS "customers" ("id" integer primary key autoincrement not null, "company_id" integer not null, "name" varchar not null, "company_name" varchar, "email" varchar, "phone_number" varchar, "address" text, "type" varchar not null default 'individual', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);
CREATE INDEX "customers_company_id_index" on "customers" ("company_id");
CREATE TABLE IF NOT EXISTS "suppliers" ("id" integer primary key autoincrement not null, "company_id" integer not null, "name" varchar not null, "contact_person" varchar, "email" varchar, "phone_number" varchar, "address" text, "nif" varchar, "rccm" varchar, "notes" text, "is_active" tinyint(1) not null default '1', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);
CREATE INDEX "suppliers_company_id_index" on "suppliers" ("company_id");
CREATE TABLE IF NOT EXISTS "products" ("id" integer primary key autoincrement not null, "company_id" integer not null, "parent_id" integer, "category_id" integer, "tax_id" integer, "unit_id" integer, "type" varchar not null default 'simple', "name" varchar not null, "sku" varchar, "description" text, "attributes" text, "purchase_price" integer, "selling_price" integer not null default '0', "unit" varchar(20) not null default 'piece', "low_stock_threshold" integer not null default '10', "is_active" tinyint(1) not null default '1', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);
CREATE UNIQUE INDEX "products_sku_unique" on "products" ("sku") where "sku" is not null;
CREATE INDEX "products_company_id_index" on "products" ("company_id");
CREATE TABLE IF NOT EXISTS "product_store" ("product_id" integer not null, "store_id" integer not null, "quantity" integer not null default '0', "low_stock_threshold" integer not null default '10', "created_at" datetime, "updated_at" datetime, primary key ("product_id", "store_id"));
CREATE TABLE IF NOT EXISTS "documents" ("id" integer primary key autoincrement not null, "company_id" integer not null, "customer_id" integer, "supplier_id" integer, "store_id" integer not null, "user_id" integer not null, "source_document_id" integer, "type" varchar not null default 'invoice', "status" varchar not null default 'draft', "validated_at" datetime, "document_number" varchar not null, "document_date" date not null, "due_date" date, "sub_total" integer not null default '0', "tax_amount" integer not null default '0', "total_amount" integer not null default '0', "paid_amount" integer not null default '0', "notes" text, "terms_and_conditions" text, "signature" text, "last_reminder_sent_at" datetime, "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);
CREATE UNIQUE INDEX "documents_number_company_unique" on "documents" ("document_number", "company_id");
CREATE INDEX "documents_company_id_index" on "documents" ("company_id");
CREATE TABLE IF NOT EXISTS "document_items" ("id" integer primary key autoincrement not null, "document_id" integer not null, "product_id" integer, "description" varchar not null, "quantity" numeric not null, "unit_price" integer not null default '0', "tax_rate" numeric not null default '0', "total_amount" integer not null default '0', "created_at" datetime, "updated_at" datetime);
CREATE INDEX "document_items_document_id_index" on "document_items" ("document_id");
CREATE TABLE IF NOT EXISTS "payments" ("id" integer primary key autoincrement not null, "company_id" integer not null, "invoice_id" integer not null, "user_id" integer not null, "amount" integer not null default '0', "status" varchar not null default 'pending', "transaction_id" varchar, "payment_date" date not null, "paid_at" datetime, "processed_at" datetime, "payment_method" varchar not null default 'cash', "reference" varchar, "notes" text, "gateway_response" text, "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "payments_transaction_id_unique" on "payments" ("transaction_id") where "transaction_id" is not null;
CREATE TABLE IF NOT EXISTS "stock_movements" ("id" integer primary key autoincrement not null, "company_id" integer not null, "product_id" integer not null, "store_id" integer not null, "user_id" integer, "source_type" varchar not null, "source_id" integer not null, "type" varchar not null default 'adjustment', "quantity" integer not null, "created_at" datetime, "updated_at" datetime);
CREATE INDEX "stock_movements_company_id_index" on "stock_movements" ("company_id");
CREATE INDEX "stock_movements_source_type_source_id_index" on "stock_movements" ("source_type", "source_id");
CREATE TABLE IF NOT EXISTS "stock_transfers" ("id" integer primary key autoincrement not null, "company_id" integer not null, "from_store_id" integer not null, "to_store_id" integer not null, "user_id" integer not null, "transfer_date" date not null, "reference" varchar, "transfer_reason" varchar, "status" varchar not null default 'pending', "notes" text, "created_at" datetime, "updated_at" datetime);
CREATE INDEX "stock_transfers_company_id_index" on "stock_transfers" ("company_id");
CREATE TABLE IF NOT EXISTS "stock_transfer_items" ("id" integer primary key autoincrement not null, "stock_transfer_id" integer not null, "product_id" integer not null, "quantity" integer not null, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "settings" ("id" integer primary key autoincrement not null, "company_id" integer not null, "key" varchar not null, "value" text, "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "settings_company_id_key_unique" on "settings" ("company_id", "key");
CREATE TABLE IF NOT EXISTS "metrics" ("id" integer primary key autoincrement not null, "company_id" integer, "type" varchar not null, "category" varchar not null, "data" text, "value" numeric, "recorded_at" datetime not null, "created_at" datetime, "updated_at" datetime);
CREATE INDEX "metrics_company_type_recorded_at_index" on "metrics" ("company_id", "type", "recorded_at");
CREATE TABLE IF NOT EXISTS "invitations" ("id" integer primary key autoincrement not null, "company_id" integer not null, "invited_by" integer not null, "email" varchar not null, "role_id" integer, "store_id" integer, "token" varchar(64) not null, "expires_at" datetime not null, "accepted_at" datetime, "invited_user_id" integer, "message" text, "status" varchar not null default 'pending', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);
CREATE UNIQUE INDEX "invitations_token_unique" on "invitations" ("token");
CREATE TABLE IF NOT EXISTS "invoices" ("id" integer primary key autoincrement not null, "invoice_number" varchar not null, "company_id" integer not null, "subscription_id" integer not null, "amount" integer not null default '0', "tax_amount" integer not null default '0', "total_amount" integer not null default '0', "status" varchar not null default 'draft', "issue_date" date not null, "due_date" date not null, "paid_at" date, "billing_address" text, "notes" text, "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "invoices_invoice_number_unique" on "invoices" ("invoice_number");
CREATE TABLE IF NOT EXISTS "billing_payments" ("id" integer primary key autoincrement not null, "invoice_id" integer not null, "amount" integer not null default '0', "payment_method" varchar not null, "transaction_id" varchar, "status" varchar not null default 'pending', "payment_date" date not null, "payment_details" text, "notes" text, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "payment_notifications" ("id" integer primary key autoincrement not null, "company_id" integer not null, "invoice_id" integer, "type" varchar not null, "title" varchar not null, "message" text not null, "status" varchar not null default 'pending', "sent_at" datetime, "scheduled_for" datetime, "notification_data" text, "created_at" datetime, "updated_at" datetime);
CREATE TABLE IF NOT EXISTS "feature_locks" ("id" integer primary key autoincrement not null, "company_id" integer not null, "feature_key" varchar(100) not null, "is_locked" tinyint(1) not null default '0', "reason" text, "locked_at" datetime, "expires_at" datetime, "locked_by" integer, "metadata" text, "created_at" datetime, "updated_at" datetime);
CREATE UNIQUE INDEX "unique_company_feature" on "feature_locks" ("company_id", "feature_key");
CREATE INDEX "idx_feature_status" on "feature_locks" ("feature_key", "is_locked");
CREATE INDEX "idx_company_locked" on "feature_locks" ("company_id", "is_locked");
CREATE INDEX "idx_expiration" on "feature_locks" ("expires_at");
CREATE TABLE IF NOT EXISTS "notifications" ("id" varchar not null primary key, "type" varchar not null, "notifiable_type" varchar not null, "notifiable_id" integer not null, "data" text not null, "read_at" datetime, "created_at" datetime, "updated_at" datetime);
CREATE INDEX "notifications_notifiable_type_notifiable_id_index" on "notifications" ("notifiable_type", "notifiable_id");

INSERT INTO "migrations" ("id", "migration", "batch") VALUES
(1,'0001_01_01_000001_create_companies_table',1),
(2,'0001_01_01_000001_create_stores_table',1),
(3,'0001_01_01_000002_create_users_table',1),
(4,'0001_01_01_000003_create_jobs_table',1),
(5,'0001_01_01_000004_create_cache_table',1),
(6,'2025_06_24_143006_create_plans_table',1),
(7,'2025_06_24_143006_create_subscriptions_table',1),
(8,'2025_06_24_143006_create_taxes_table',1),
(9,'2025_06_24_143007_create_categories_table',1),
(10,'2025_06_24_143007_create_products_table',1),
(11,'2025_06_24_143008_create_customers_table',1),
(12,'2025_06_24_143008_create_documents_table',1),
(13,'2025_06_24_143008_create_payments_table',1),
(14,'2025_06_24_143009_create_document_items_table',1),
(15,'2025_06_24_143009_create_stock_movements_table',1),
(16,'2025_06_24_143009_create_stock_transfers_table',1),
(17,'2025_06_24_143010_create_stock_transfer_items_table',1),
(18,'2025_06_24_143039_create_product_store_table',1),
(19,'2025_06_24_143720_create_permission_tables',1),
(20,'2025_06_24_143803_create_media_table',1),
(21,'2025_06_24_144804_add_tables_with_foreign_keys',1),
(22,'2025_06_24_203542_create_settings_table',1),
(23,'2025_06_28_020556_add_validated_at_to_documents_table',1),
(24,'2025_06_30_200733_add_missing_fields_to_tables',1),
(25,'2025_06_30_203132_create_units_table',1),
(26,'2025_06_30_203238_modify_products_for_units',1),
(27,'2025_07_05_112003_create_suppliers_table',1),
(28,'2025_07_05_145803_add_details_to_suppliers_table',1),
(29,'2025_07_10_092544_add_supplier_id_to_documents_table',1),
(30,'2025_07_10_223340_make_company_id_nullable_in_users_table',1),
(31,'2025_07_12_032152_add_company_id_to_permission_tables',2),
(32,'2025_07_12_032634_add_is_global_admin_to_users_table',2),
(33,'2025_07_12_032901_create_metrics_table',2),
(34,'2025_07_12_035240_add_is_active_to_companies_table',2),
(35,'2025_07_14_044404_ensure_company_fields_nullable',2),
(36,'2025_07_14_174251_extend_stores_table_for_country_branches',2),
(37,'2025_07_14_211622_add_performance_indexes_to_main_tables',2),
(38,'2025_07_15_220556_create_invitations_table',2),
(39,'2025_07_15_223552_add_reference_column_to_stock_transfers_table',2),
(40,'2025_07_15_230552_add_transfer_reason_to_stock_transfers_table',2),
(41,'2025_07_24_235920_add_subscription_fields_to_plans_table',2),
(42,'2025_07_25_024330_add_last_login_at_to_users_table',2),
(43,'2025_07_25_213204_create_invoices_table',2),
(44,'2025_07_25_213309_create_billing_payments_table',2),
(45,'2025_07_26_035615_add_team_id_to_permission_tables',2),
(46,'2025_07_26_174712_create_payment_notifications_table',2),
(47,'2025_08_18_162535_add_unit_id_to_products_table',2),
(48,'2025_08_18_171759_convert_amounts_to_integers_for_fcfa',2),
(49,'2025_08_18_220806_create_feature_locks_table',2),
(50,'2025_08_19_122823_create_notifications_table',2),
(51,'2025_08_19_191326_update_payments_table_for_saas',2),
(52,'2026_04_05_010456_create_tenant_modules_table',3),
(53,'2026_04_05_012102_create_drivers_table',3),
(54,'2026_04_05_012103_create_vehicles_table',3),
(55,'2026_04_05_012103_create_zones_table',3),
(56,'2026_04_05_012104_create_delivery_trips_table',3),
(57,'2026_04_05_012105_create_delivery_items_table',3),
(58,'2026_04_05_012106_create_delivery_expense_categories_table',3),
(59,'2026_04_05_012107_create_delivery_expenses_table',3);
CREATE TABLE IF NOT EXISTS "tenant_modules" ("id" integer primary key autoincrement not null, "company_id" integer not null, "module_key" varchar not null, "is_enabled" tinyint(1) not null default '0', "config" text, "enabled_at" datetime, "enabled_by" integer, "created_at" datetime, "updated_at" datetime, foreign key("company_id") references "companies"("id") on delete cascade, foreign key("enabled_by") references "users"("id") on delete set null);
CREATE UNIQUE INDEX "tenant_modules_company_id_module_key_unique" on "tenant_modules" ("company_id", "module_key");
CREATE INDEX "tenant_modules_module_key_index" on "tenant_modules" ("module_key");
CREATE TABLE IF NOT EXISTS "drivers" ("id" integer primary key autoincrement not null, "company_id" integer not null, "name" varchar not null, "phone" varchar, "license_number" varchar, "base_salary" integer not null default '0', "is_active" tinyint(1) not null default '1', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime, foreign key("company_id") references "companies"("id") on delete cascade);
CREATE INDEX "drivers_company_id_index" on "drivers" ("company_id");
CREATE TABLE IF NOT EXISTS "vehicles" ("id" integer primary key autoincrement not null, "company_id" integer not null, "plate_number" varchar not null, "brand" varchar, "model" varchar, "is_active" tinyint(1) not null default '1', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime, foreign key("company_id") references "companies"("id") on delete cascade);
CREATE INDEX "vehicles_company_id_index" on "vehicles" ("company_id");
CREATE TABLE IF NOT EXISTS "zones" ("id" integer primary key autoincrement not null, "company_id" integer not null, "name" varchar not null, "city" varchar not null, "mission_allowance" integer not null default '5000', "created_at" datetime, "updated_at" datetime, foreign key("company_id") references "companies"("id") on delete cascade);
CREATE INDEX "zones_company_id_index" on "zones" ("company_id");
CREATE TABLE IF NOT EXISTS "delivery_trips" ("id" integer primary key autoincrement not null, "company_id" integer not null, "driver_id" integer not null, "vehicle_id" integer, "zone_id" integer, "trip_date" date not null, "status" varchar not null default 'draft', "loaded_crates" integer, "returned_crates" integer, "total_revenue" integer, "total_margin" integer, "total_expenses" integer, "bank_percentage" integer not null default '80', "bank_amount" integer, "cash_amount" integer, "funds_amount" integer, "mission_allowance_amount" integer, "notes" text, "loaded_at" datetime, "departed_at" datetime, "returned_at" datetime, "closed_at" datetime, "closed_by" integer, "created_at" datetime, "updated_at" datetime, foreign key("company_id") references "companies"("id") on delete cascade, foreign key("driver_id") references "drivers"("id") on delete cascade, foreign key("vehicle_id") references "vehicles"("id") on delete set null, foreign key("zone_id") references "zones"("id") on delete set null, foreign key("closed_by") references "users"("id") on delete set null);
CREATE INDEX "delivery_trips_company_id_index" on "delivery_trips" ("company_id");
CREATE INDEX "delivery_trips_driver_id_index" on "delivery_trips" ("driver_id");
CREATE INDEX "delivery_trips_trip_date_index" on "delivery_trips" ("trip_date");
CREATE INDEX "delivery_trips_status_index" on "delivery_trips" ("status");
CREATE TABLE IF NOT EXISTS "delivery_items" ("id" integer primary key autoincrement not null, "trip_id" integer not null, "customer_id" integer, "product_id" integer, "product_ref" varchar, "product_designation" varchar not null, "qty_delivered" integer not null default '0', "qty_returned" integer not null default '0', "unit_price" integer not null default '0', "margin_per_unit" integer not null default '0', "notes" text, "created_at" datetime, "updated_at" datetime, foreign key("trip_id") references "delivery_trips"("id") on delete cascade, foreign key("customer_id") references "customers"("id") on delete set null, foreign key("product_id") references "products"("id") on delete set null);
CREATE INDEX "delivery_items_trip_id_index" on "delivery_items" ("trip_id");
CREATE INDEX "delivery_items_product_id_index" on "delivery_items" ("product_id");
CREATE TABLE IF NOT EXISTS "delivery_expense_categories" ("id" integer primary key autoincrement not null, "company_id" integer not null, "name" varchar not null, "is_default" tinyint(1) not null default '0', "sort_order" integer not null default '0', "created_at" datetime, "updated_at" datetime, foreign key("company_id") references "companies"("id") on delete cascade);
CREATE INDEX "delivery_expense_categories_company_id_index" on "delivery_expense_categories" ("company_id");
CREATE TABLE IF NOT EXISTS "delivery_expenses" ("id" integer primary key autoincrement not null, "trip_id" integer not null, "category_id" integer, "label" varchar not null, "amount" integer not null, "created_at" datetime, "updated_at" datetime, foreign key("trip_id") references "delivery_trips"("id") on delete cascade, foreign key("category_id") references "delivery_expense_categories"("id") on delete set null);
CREATE INDEX "delivery_expenses_trip_id_index" on "delivery_expenses" ("trip_id");
