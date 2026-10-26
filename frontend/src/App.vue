<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api'

// ----- LIST -----
const students = ref([])
const loading = ref(true)
const error = ref('')

async function loadStudents() {
  loading.value = true
  error.value = ''
  try {
    const response = await axios.get(`${API_URL}/students`)
    students.value = response.data
  } catch (err) {
    error.value = 'Could not load students. Is the Laravel server running?'
  } finally {
    loading.value = false
  }
}

// ----- FORM (used for both adding and editing) -----
const form = reactive({ name: '', email: '', course: '' })
const errors = ref({})
const formError = ref('')
const saving = ref(false)
const editingId = ref(null)   // null = adding, a number = editing that student

function resetForm() {
  form.name = ''
  form.email = ''
  form.course = ''
  errors.value = {}
  formError.value = ''
  editingId.value = null
}

// Copy a student's data into the form and switch to "edit mode"
function startEdit(student) {
  editingId.value = student.id
  form.name = student.name
  form.email = student.email
  form.course = student.course
  errors.value = {}
  formError.value = ''
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Save: PUT if we are editing, POST if we are adding
async function saveStudent() {
  errors.value = {}
  formError.value = ''
  saving.value = true
  try {
    if (editingId.value) {
      // PUT http://localhost:8000/api/students/{id}
      await axios.put(`${API_URL}/students/${editingId.value}`, form)
    } else {
      // POST http://localhost:8000/api/students
      await axios.post(`${API_URL}/students`, form)
    }
    resetForm()
    await loadStudents()
  } catch (err) {
    if (err.response && err.response.status === 422) {
      errors.value = err.response.data.errors
    } else {
      formError.value = 'Could not save the student. Is the Laravel server running?'
    }
  } finally {
    saving.value = false
  }
}

// ----- DELETE -----
async function deleteStudent(student) {
  // confirm() shows OK / Cancel. Cancel returns false, so we stop here.
  if (!confirm(`Delete ${student.name}?`)) {
    return
  }
  error.value = ''
  try {
    // DELETE http://localhost:8000/api/students/{id}
    await axios.delete(`${API_URL}/students/${student.id}`)
    // If we were editing the student we just deleted, leave edit mode
    if (editingId.value === student.id) {
      resetForm()
    }
    await loadStudents()
  } catch (err) {
    error.value = 'Could not delete the student.'
  }
}

onMounted(loadStudents)
</script>

<template>
  <div class="container">
    <h1>Student Management System</h1>

    <!-- Add / Edit form -->
    <form @submit.prevent="saveStudent" class="form">
      <h2>{{ editingId ? 'Edit Student' : 'Add Student' }}</h2>

      <label>Full name</label>
      <input v-model="form.name" type="text" placeholder="Full name" />
      <small v-if="errors.name" class="error">{{ errors.name[0] }}</small>

      <label>Email</label>
      <input v-model="form.email" type="text" placeholder="Email" />
      <small v-if="errors.email" class="error">{{ errors.email[0] }}</small>

      <label>Course</label>
      <input v-model="form.course" type="text" placeholder="Course" />
      <small v-if="errors.course" class="error">{{ errors.course[0] }}</small>

      <p v-if="formError" class="error">{{ formError }}</p>

      <button type="submit" :disabled="saving">
        {{ saving ? 'Saving...' : (editingId ? 'Save Changes' : 'Add Student') }}
      </button>
      <button v-if="editingId" type="button" @click="resetForm">Cancel</button>
    </form>

    <!-- Student list -->
    <p v-if="loading">Loading students...</p>
    <p v-else-if="error" class="error">{{ error }}</p>
    <p v-else-if="students.length === 0">No students yet.</p>

    <table v-else>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Course</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="student in students" :key="student.id">
          <td>{{ student.id }}</td>
          <td>{{ student.name }}</td>
          <td>{{ student.email }}</td>
          <td>{{ student.course }}</td>
          <td>{{ new Date(student.created_at).toLocaleDateString() }}</td>
          <td>
            <button @click="startEdit(student)">Edit</button>
            <button @click="deleteStudent(student)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 24px 16px 60px;
}

h1 {
  text-align: center;
  color: #4f46e5; /* indigo */
  margin-bottom: 24px;
}

/* ---------- Form card ---------- */
.form {
  display: flex;
  flex-direction: column;
  gap: 6px;
  background: #ffffff;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  margin-bottom: 30px;
}

.form h2 {
  margin: 0 0 8px;
}

.form label {
  font-weight: 600;
  font-size: 14px;
  margin-top: 6px;
}

.form input {
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 15px;
}

.form input:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}

/* ---------- Buttons ---------- */
button {
  cursor: pointer;
  border: none;
  border-radius: 8px;
  font-size: 14px;
}

.form button {
  padding: 11px;
  margin-top: 10px;
  font-size: 15px;
}

.form button[type='submit'] {
  background: #4f46e5;
  color: #ffffff;
}

.form button[type='submit']:hover {
  background: #4338ca;
}

.form button[type='button'] {
  background: #e5e7eb;
  color: #1f2937;
}

.form button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* ---------- Table ---------- */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden; /* clips the corners so they stay rounded */
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

th {
  background: #4f46e5;
  color: #ffffff;
  text-align: left;
  padding: 12px;
}

td {
  padding: 12px;
  border-bottom: 1px solid #e5e7eb;
}

tbody tr:nth-child(even) {
  background: #f9fafb;
}

tbody tr:hover {
  background: #eef2ff;
}

tbody tr:last-child td {
  border-bottom: none;
}

td button {
  padding: 6px 12px;
  margin-right: 6px;
  color: #ffffff;
}

td button:first-child {
  background: #0ea5e9; /* blue: Edit */
}

td button:last-child {
  background: #dc2626; /* red: Delete */
  margin-right: 0;
}

td button:hover {
  opacity: 0.85;
}

/* ---------- Messages ---------- */
.error {
  color: #dc2626;
  font-size: 13px;
}
</style>