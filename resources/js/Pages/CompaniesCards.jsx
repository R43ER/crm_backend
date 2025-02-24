// src/pages/Companies.jsx
import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { Container, Row, Col, Card, Alert, Spinner, Button } from 'react-bootstrap';
import EditableCell from '../components/EditableCell';
import PopupCreateCompany from '../components/PopupCreateCompany';
import PopupEditCompany from '../components/PopupEditCompany';
import DeleteConfirmationModal from '../components/DeleteConfirmationModal';
import { API_BASE_URL } from '../config/api';

function Companies() {
  const [companies, setCompanies] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [showCreatePopup, setShowCreatePopup] = useState(false);
  const [showEditPopup, setShowEditPopup] = useState(false);
  const [showDeletePopup, setShowDeletePopup] = useState(false);
  const [selectedCompany, setSelectedCompany] = useState(null);
  const token = localStorage.getItem('token');

  const fetchCompanies = useCallback(async () => {
    try {
      const response = await axios.get(
        'http://' + window.location.hostname + '/api/api/companies',
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setCompanies(response.data);
      setLoading(false);
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка загрузки компаний');
      setLoading(false);
    }
  }, [token]);

  useEffect(() => {
    fetchCompanies();
  }, [fetchCompanies]);

  const handleUpdateCompany = async (id, field, newValue) => {
    try {
      await axios.put(
        'http://' + window.location.hostname + `/api/api/companies/${id}`,
        { [field]: newValue },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setCompanies(prev =>
        prev.map(company =>
          company.id === id ? { ...company, [field]: newValue } : company
        )
      );
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка обновления компании');
    }
  };

  const handleDeleteCompany = async () => {
    try {
      await axios.delete(
        'http://' + window.location.hostname + `/api/api/companies/${selectedCompany.id}`,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setShowDeletePopup(false);
      fetchCompanies();
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка удаления компании');
    }
  };

  return (
    <Container className="mt-5">
      <h2>Компании</h2>
      <Button variant="primary" onClick={() => setShowCreatePopup(true)} className="mb-3">
        Создать компанию
      </Button>
      {loading && (
        <div className="text-center my-3">
          <Spinner animation="border" variant="primary" />
        </div>
      )}
      {error && <Alert variant="danger">{error}</Alert>}
      {!loading && !error && (
        <Row>
          {companies.map(company => (
            <Col key={company.id} xs={12} sm={6} md={4} lg={3} className="mb-4">
              <Card>
                <Card.Body>
                  <Card.Title>
                    <EditableCell
                      value={company.name}
                      onSave={(newValue) =>
                        handleUpdateCompany(company.id, 'name', newValue)
                      }
                    />
                  </Card.Title>
                  <Card.Text>
                    <strong>Телефон:</strong>{' '}
                    <EditableCell
                      value={company.phone}
                      onSave={(newValue) =>
                        handleUpdateCompany(company.id, 'phone', newValue)
                      }
                    />
                    <br />
                    <strong>Email:</strong>{' '}
                    <EditableCell
                      value={company.email}
                      onSave={(newValue) =>
                        handleUpdateCompany(company.id, 'email', newValue)
                      }
                    />
                    <br />
                    <strong>Веб-сайт:</strong>{' '}
                    <EditableCell
                      value={company.web}
                      onSave={(newValue) =>
                        handleUpdateCompany(company.id, 'web', newValue)
                      }
                    />
                    <br />
                    <strong>Адрес:</strong>{' '}
                    <EditableCell
                      value={company.address}
                      onSave={(newValue) =>
                        handleUpdateCompany(company.id, 'address', newValue)
                      }
                    />
                  </Card.Text>
                  <div className="d-flex justify-content-between">
                    <Button
                      variant="warning"
                      size="sm"
                      onClick={() => {
                        setSelectedCompany(company);
                        setShowEditPopup(true);
                      }}
                    >
                      Редактировать
                    </Button>
                    <Button
                      variant="danger"
                      size="sm"
                      onClick={() => {
                        setSelectedCompany(company);
                        setShowDeletePopup(true);
                      }}
                    >
                      Удалить
                    </Button>
                  </div>
                </Card.Body>
              </Card>
            </Col>
          ))}
        </Row>
      )}

      <PopupCreateCompany
        show={showCreatePopup}
        onClose={() => setShowCreatePopup(false)}
        onSuccess={fetchCompanies}
      />

      {selectedCompany && (
        <PopupEditCompany
          show={showEditPopup}
          onClose={() => setShowEditPopup(false)}
          onSuccess={fetchCompanies}
          company={selectedCompany}
        />
      )}

      {selectedCompany && (
        <DeleteConfirmationModal
          show={showDeletePopup}
          onClose={() => setShowDeletePopup(false)}
          onConfirm={handleDeleteCompany}
          contact={selectedCompany}
        />
      )}
    </Container>
  );
}

export default Companies;
