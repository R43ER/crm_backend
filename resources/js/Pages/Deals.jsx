// src/pages/Deals.jsx
import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { Container, Table, Alert, Spinner, Button } from 'react-bootstrap';
import EditableCell from '../components/EditableCell';
import PopupCreateDeal from '../components/PopupCreateDeal';
import PopupEditDeal from '../components/PopupEditDeal';
import DeleteConfirmationModal from '../components/DeleteConfirmationModal';
import EditableSelect from '../components/EditableSelect';
import { API_BASE_URL } from '../config/api';

function Deals() {
  const [deals, setDeals] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const [showCreatePopup, setShowCreatePopup] = useState(false);
  const [showEditPopup, setShowEditPopup] = useState(false);
  const [showDeletePopup, setShowDeletePopup] = useState(false);
  const [selectedDeal, setSelectedDeal] = useState(null);

  const [employees, setEmployees] = useState([]);
  const token = localStorage.getItem('token');

  const fetchDeals = useCallback(async () => {
    try {
      const response = await axios.get(
        'http://' + window.location.hostname + '/api/api/deals',
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setDeals(response.data);
      setLoading(false);
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка загрузки сделок');
      setLoading(false);
    }
  }, [token]);

  const fetchEmployees = useCallback(async () => {
    try {
      const response = await axios.get(
        'http://' + window.location.hostname + '/api/api/users',
        { headers: { Authorization: `Bearer ${token}` } }
      );
      const options = response.data.map(user => ({
        value: user.id,
        label: user.name,
      }));
      setEmployees(options);
    } catch (err) {
      console.error("Ошибка загрузки сотрудников:", err.response?.data?.message);
    }
  }, [token]);

  useEffect(() => {
    fetchDeals();
    fetchEmployees();
  }, [fetchDeals, fetchEmployees]);

  const handleUpdateDeal = async (id, field, newValue) => {
    try {
      await axios.put(
        'http://' + window.location.hostname + `/api/api/deals/${id}`,
        { [field]: newValue },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setDeals(prev =>
        prev.map(deal =>
          deal.id === id ? { ...deal, [field]: newValue } : deal
        )
      );
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка обновления сделки');
    }
  };

  const handleDeleteDeal = async () => {
    try {
      await axios.delete(
        'http://' + window.location.hostname + `/api/api/deals/${selectedDeal.id}`,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setShowDeletePopup(false);
      fetchDeals();
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка удаления сделки');
    }
  };

  return (
    <Container className="mt-5">
      <h2>Сделки</h2>
      <Button variant="primary" onClick={() => setShowCreatePopup(true)} className="mb-3">
        Создать сделку
      </Button>
      {loading && (
        <div className="text-center my-3">
          <Spinner animation="border" variant="primary" />
        </div>
      )}
      {error && <Alert variant="danger">{error}</Alert>}
      {!loading && !error && (
        <Table striped bordered hover responsive>
          <thead>
            <tr>
              <th>ID</th>
              <th>Название</th>
              <th>Бюджет</th>
              <th>Статус</th>
              <th>Ответственный</th>
              <th>Действия</th>
            </tr>
          </thead>
          <tbody>
            {deals.map(deal => (
              <tr key={deal.id}>
                <td>{deal.id}</td>
                <td>
                  <EditableCell
                    value={deal.title}
                    onSave={(newValue) =>
                      handleUpdateDeal(deal.id, 'title', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={deal.budget ? deal.budget.toString() : ''}
                    onSave={(newValue) =>
                      handleUpdateDeal(deal.id, 'budget', parseFloat(newValue))
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={deal.status}
                    onSave={(newValue) =>
                      handleUpdateDeal(deal.id, 'status', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableSelect
                    value={deal.responsible_user_id}
                    options={employees}
                    onSave={(newValue) =>
                      handleUpdateDeal(deal.id, 'responsible_user_id', newValue)
                    }
                  />
                </td>
                <td>
                  <Button
                    variant="warning"
                    size="sm"
                    onClick={() => {
                      setSelectedDeal(deal);
                      setShowEditPopup(true);
                    }}
                    className="me-2"
                  >
                    Редактировать
                  </Button>
                  <Button
                    variant="danger"
                    size="sm"
                    onClick={() => {
                      setSelectedDeal(deal);
                      setShowDeletePopup(true);
                    }}
                  >
                    Удалить
                  </Button>
                </td>
              </tr>
            ))}
          </tbody>
        </Table>
      )}

      <PopupCreateDeal
        show={showCreatePopup}
        onClose={() => setShowCreatePopup(false)}
        onSuccess={fetchDeals}
      />

      {selectedDeal && (
        <PopupEditDeal
          show={showEditPopup}
          onClose={() => setShowEditPopup(false)}
          onSuccess={fetchDeals}
          deal={selectedDeal}
        />
      )}

      {selectedDeal && (
        <DeleteConfirmationModal
          show={showDeletePopup}
          onClose={() => setShowDeletePopup(false)}
          onConfirm={handleDeleteDeal}
          contact={selectedDeal}
        />
      )}
    </Container>
  );
}

export default Deals;
