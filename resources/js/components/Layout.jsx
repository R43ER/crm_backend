/** @jsxImportSource react */

import { useState, useEffect } from 'react';
import { Container, Navbar, Nav, NavDropdown, Row, Col } from 'react-bootstrap';
import { Link, Outlet } from 'react-router-dom';

const Layout = () => {
  const [user, setUser] = useState(null);

  // При монтировании компонента Layout читаем данные из localStorage
  useEffect(() => {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      setUser(JSON.parse(storedUser));
    }
  }, []);

  return (
    <>
      {/* Верхний Navbar */}
      <Navbar bg="dark" variant="dark" expand="lg">
        <Container fluid>
          {/* Слева – логотип с ссылкой на главную */}
          <Navbar.Brand as={Link} to="/">
            MyApp
          </Navbar.Brand>
          <Navbar.Toggle aria-controls="navbar-nav" />
          <Navbar.Collapse id="navbar-nav">
            {/* Справа – выпадающий список текущего аккаунта */}
            <Nav className="ms-auto">
              {user ? (
                <NavDropdown title={user.name} id="account-dropdown" align="end">
                  <NavDropdown.Item as={Link} to="/profile">
                    Профиль
                  </NavDropdown.Item>
                  <NavDropdown.Divider />
                  <NavDropdown.Item as={Link} to="/logout">
                    Выход
                  </NavDropdown.Item>
                </NavDropdown>
              ) : (
                <Nav.Link as={Link} to="/login">
                  Войти
                </Nav.Link>
              )}
            </Nav>
          </Navbar.Collapse>
        </Container>
      </Navbar>

      {/* Основная область: sidebar и контент */}
      <Container fluid>
        <Row>
          {/* Боковая панель */}
          <Col md={1} className="bg-light p-3" style={{ minHeight: 'calc(100vh - 56px)' }}>
            <Nav defaultActiveKey="/" className="flex-column">
              <Nav.Link as={Link} to="/">
                Главная
              </Nav.Link>
              <Nav.Link as={Link} to="/contacts">
                Контакты
              </Nav.Link>
              <Nav.Link as={Link} to="/companies">
                Компании
              </Nav.Link>
              <Nav.Link as={Link} to="/tasks">
                Задания
              </Nav.Link>
              <Nav.Link as={Link} to="/deals">
                Сделки
              </Nav.Link>
              {/* Добавьте другие ссылки по необходимости */}
            </Nav>
          </Col>

          {/* Основной контент */}
          <Col md={10} className="p-3">
            <Outlet />
          </Col>
        </Row>
      </Container>
    </>
  );
};

export default Layout;
