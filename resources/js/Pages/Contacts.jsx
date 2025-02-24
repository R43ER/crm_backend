// src/pages/Contacts.jsx
import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { Container, Table, Alert, Spinner, Button } from 'react-bootstrap';
import EditableCell from '../components/EditableCell';
import PopupCreateContact from '../components/PopupCreateContact';
import PopupEditContact from '../components/PopupEditContact';
import DeleteConfirmationModal from '../components/DeleteConfirmationModal';
import { API_BASE_URL } from '../config/api';

function Contacts() {
  const [contacts, setContacts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const [showCreatePopup, setShowCreatePopup] = useState(false);
  const [showEditPopup, setShowEditPopup] = useState(false);
  const [showDeletePopup, setShowDeletePopup] = useState(false);

  const [selectedContact, setSelectedContact] = useState(null);
  const token = localStorage.getItem('token');

  const fetchContacts = useCallback(async () => {
    try {
      const response = await axios.get(
        'http://' + window.location.hostname + '/api/api/contacts',
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setContacts(response.data);
      setLoading(false);
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка загрузки контактов');
      setLoading(false);
    }
  }, [token]);

  useEffect(() => {
    fetchContacts();
  }, [fetchContacts]);

  const handleUpdateContact = async (id, field, newValue) => {
    try {
      await axios.put(
        'http://' + window.location.hostname + `/api/api/contacts/${id}`,
        { [field]: newValue },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setContacts(prev =>
        prev.map(contact =>
          contact.id === id ? { ...contact, [field]: newValue } : contact
        )
      );
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка обновления контакта');
    }
  };

  const handleDeleteContact = async () => {
    try {
      await axios.delete(
        'http://' + window.location.hostname + `/api/api/contacts/${selectedContact.id}`,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setShowDeletePopup(false);
      fetchContacts();
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка удаления контакта');
    }
  };

  return (
    <Container className="mt-5">
      <h2>Контакты</h2>
      <Button variant="primary" onClick={() => setShowCreatePopup(true)} className="mb-3">
        Создать контакт
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
              <th>Имя</th>
              <th>Фамилия</th>
              <th>Email</th>
              <th>Телефон</th>
              <th>Должность</th>
              <th>Действия</th>
            </tr>
          </thead>
          <tbody>
            {contacts.map(contact => (
              <tr key={contact.id}>
                <td>{contact.id}</td>
                <td>
                  <EditableCell
                    value={contact.first_name}
                    onSave={(newValue) =>
                      handleUpdateContact(contact.id, 'first_name', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={contact.last_name}
                    onSave={(newValue) =>
                      handleUpdateContact(contact.id, 'last_name', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={contact.email}
                    onSave={(newValue) =>
                      handleUpdateContact(contact.id, 'email', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={contact.phone}
                    onSave={(newValue) =>
                      handleUpdateContact(contact.id, 'phone', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={contact.position}
                    onSave={(newValue) =>
                      handleUpdateContact(contact.id, 'position', newValue)
                    }
                  />
                </td>
                <td>
                  <Button
                    variant="warning"
                    size="sm"
                    onClick={() => {
                      setSelectedContact(contact);
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
                      setSelectedContact(contact);
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

      <PopupCreateContact
        show={showCreatePopup}
        onClose={() => setShowCreatePopup(false)}
        onSuccess={fetchContacts}
      />

      {selectedContact && (
        <PopupEditContact
          show={showEditPopup}
          onClose={() => setShowEditPopup(false)}
          onSuccess={fetchContacts}
          contact={selectedContact}
        />
      )}

      {selectedContact && (
        <DeleteConfirmationModal
          show={showDeletePopup}
          onClose={() => setShowDeletePopup(false)}
          onConfirm={handleDeleteContact}
          contact={selectedContact}
        />
      )}
    </Container>
  );
}

export default Contacts;
