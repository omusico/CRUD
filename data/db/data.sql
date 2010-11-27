SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";



INSERT INTO `accounts` (`account_name`, `fullname`) VALUES
('developer_andrew', 'Andrew S. - web developer'),
('developer_john', 'John T. - web developer'),
('developer_elvis', 'Elvis C. -  web developer'),
('designer_rob', 'Rob F. - graphic designer'),
('bugtester_daniel', 'Daniel C. - bug tester'),
('technical_manager_robert', 'Robert Smith');


INSERT INTO `bugs` (`bug_id`, `bug_description`, `bug_status`, `reported_by`, `assigned_to`, `verified_by`) VALUES
(1, 'cache not working for news in homepage', 'open', 'developer_elvis', 'developer_elvis', 'designer_rob'),
(2, 'Paginator not working in news', 'fixed', 'designer_rob', 'bugtester_daniel', 'developer_elvis');


INSERT INTO `bugs_products` (`bug_id`, `product_id`) VALUES
(1, 1),
(1, 2),
(1, 4),
(2, 1);


INSERT INTO `products` (`product_id`, `product_name`) VALUES
(1, 'Main website'),
(2, 'Blog'),
(3, 'Administrative software'),
(4, 'Product n.6'),
(5, 'Other product');
