<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-6">
                <div id="todo-container" class="p-3">
                    <section id="todo-header" class="mt-3">
                        <h3 class="text-center">Add Property</h3>
                    </section>
                    <section id="todo-errors">
                        <div v-if="createPropertyForm.errors.length > 0" class="alert alert-warning alert-dismissible fade show" role="alert">
                            <span v-for="(error, index) in createPropertyForm.errors" :key="index">{{ error }}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div v-if="createPropertyForm.isCreated" class="alert alert-success alert-dismissible fade show" role="alert">
                            <span>Property added successfully</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </section>
                    <section id="add-todo-form" class="my-3">
                        <form>
                            <div class="align-items-center">

                              <div class="col-sm-6 form-group">
                                <input
                                    v-model="createPropertyForm.title"
                                    v-on:keyup.enter="addTodo"
                                    minlength="5" maxlength="50"
                                    placeholder="Enter Title"
                                    type="text" class="form-control mr-3"></br>
                                </div>
                                 <div class="col-sm-6 form-group">
                                    <textarea
                                    placeholder="Enter Description"
                                     v-model="createPropertyForm.description"
                                    class="form-control mr-3"></textarea></br>
                                  </div>
                                <div class="col-sm-6 form-group">
                                    <input
                                    v-model="createPropertyForm.price"
                                    placeholder="Enter price"
                                    type="text" class="form-control mr-3">
                                </div>    

                                <div class=" col-sm-6 form-group">
                                    <label for="description">Picture</label>
                                     <input type="file" id="file" ref="file" />
                                </div>
                                 <div class="col-sm-2 form-group">    
                                <button v-if="createPropertyForm.isSubmitting" class="btn btn-primary" type="button" disabled>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                    <span class="sr-only">Loading...</span>
                                </button>
                                <button v-else @click.prevent="addTodo" class="btn btn-primary">Add</button>
                                 </div>
                            </div>
                        </form>
                    </section>
                    <section id="todo-actions"></section>
                    <section id="todo-list">
                        <ul class="list-group">
                            <div v-if="properties.isLoading" class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                                 <table class="table table-hover table-bordered" id="example"  v-if="!properties.isLoading && properties.data.length > 0">
                                <thead>
                                <tr>
                                    
                                    <th>Property Title</th>
                                    <th>Property Description</th>
                                    <th>Property Price</th>
                                    <th>Property image</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="property in properties.data" :key="property.id" >
                                    <td>{{property.title}}</td>
                                    <td>{{property.description}}</td>
                                    <td>{{property.price}}</td>
                                    <td>
                                    <img :src="'uploads/'+ property.image" height="50" width="50">
                                    </td>
                                </tr>
                                
                                </tbody>
                            </table>    

                            <li v-if="!properties.isLoading && properties.data.length === 0" class="list-group-item list-group-item-action list-group-item-warning">
                                No Property found.
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
       
    </div>
</template>

<script>

//Bootstrap and jQuery libraries
import 'bootstrap/dist/css/bootstrap.min.css';
import 'jquery/dist/jquery.min.js';
//Datatable Modules
import "datatables.net-dt/js/dataTables.dataTables"
import "datatables.net-dt/css/jquery.dataTables.min.css"


    export default {
        name: 'PropertyList',
        data() {
            return {
                properties: {
                    isLoading: false,
                    data: []
                },
                createPropertyForm: {
                    isSubmitting: false,
                    isCreated: false,
                    title: undefined,
                    description: undefined,
                    price: undefined,
                    file: undefined,
                    errors: []
                },
                error: undefined
            }
        },
        mounted() {
            this.loadTodos();
        },
        methods: {
            loadTodos() {
                // update loader to loading
                this.properties.isLoading = true;

                axios.get('/properties')
                .then((response) => {
                    this.properties.data = response.data;
                     $('#example').DataTable();
                })
                .catch((error) => {
                    //TODO: update with your logic on how to handle this error.
                    console.log(error.message);
                    this.error = 'Unable to load propertie. View log for more information';
                })
                .finally(() => {
                    // disable loader
                    this.properties.isLoading = false;
                })
            },

            /**
             * Create a TodoItem.
             * Uses payload in the this.createPropertyForm param
             */
            addTodo() {
                // update loader to loading
                this.createPropertyForm.isSubmitting = true;

                this.createPropertyForm.file = this.$refs.file.files[0];
                
                 let formData = new FormData();
                 formData.append('file', this.createPropertyForm.file);
                 formData.append('isSubmitting', this.createPropertyForm.isSubmitting);
                 formData.append('isCreated', this.createPropertyForm.isCreated);
                 formData.append('title', this.createPropertyForm.title);
                 formData.append('description', this.createPropertyForm.description);
                 formData.append('price', this.createPropertyForm.price);
                 formData.append('errors', this.createPropertyForm.errors);
                

                axios.post('/properties', formData, {
                        headers: {
                        'Content-Type': 'multipart/form-data'
                        }
                    })
                            .then((response) => {   
                    this.createPropertyForm.errors = [];
                    this.createPropertyForm.title = undefined;
                    this.createPropertyForm.description = undefined;
                    this.createPropertyForm.price = undefined;
                    this.createPropertyForm.isCreated = true;
                    this.createPropertyForm.file=undefined;
                    this.loadTodos();
                })
                .catch((error) => {
                    /**
                     * Check for form validation error. Laravel return http code 422
                     * _ lodash is already loaded by laravel. check resources/js/bootstrap.js
                     */
                    if (error.response && error.response.status === 422) {
                        this.createPropertyForm.errors = _.flatten(_.toArray(error.response.data.errors));
                    } else {
                        //TODO: update with your logic on how to handle this error.
                        console.log(error.message);
                        this.error = 'Unable to add todo. View log for more information';
                    }
                })
                .finally(() => {
                    // disable loader
                    this.createPropertyForm.isSubmitting = false;
                })
            }
      
        }
    }
</script>

