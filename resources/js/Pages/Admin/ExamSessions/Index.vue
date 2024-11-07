<template>
    <Head>
        <title>Sesi Ujian - Aplikasi Ujian Online</title>
    </Head>
    <div class="container-fluid mb-5 mt-5">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-3 col-12 mb-2">
                        <Link
                            href="/admin/exam_sessions/create"
                            class="btn btn-md btn-primary border-0 shadow w-100"
                            type="button"
                            ><i class="fa fa-plus-circle"></i> Tambah</Link
                        >
                    </div>
                    <div class="col-md-9 col-12 mb-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input
                                    type="text"
                                    class="form-control border-0 shadow"
                                    v-model="search"
                                    placeholder="masukkan kata kunci dan enter..."
                                />
                                <span class="input-group-text border-0 shadow">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-centered table-nowrap mb-0 rounded"
                            >
                                <thead class="thead-dark">
                                    <tr class="border-0">
                                        <th
                                            class="border-0 rounded-start"
                                            style="width: 5%"
                                        >
                                            No.
                                        </th>
                                        <th class="border-0">Ujian</th>
                                        <th class="border-0">Sesi</th>
                                        <th class="border-0">Siswa</th>
                                        <th class="border-0">Token</th>
                                        <!-- New Token Column -->
                                        <th class="border-0">Mulai</th>
                                        <th class="border-0">Selesai</th>
                                        <th
                                            class="border-0 rounded-end"
                                            style="width: 15%"
                                        >
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            exam_session, index
                                        ) in exam_sessions.data"
                                        :key="index"
                                    >
                                        <td class="fw-bold text-center">
                                            {{
                                                ++index +
                                                (exam_sessions.current_page -
                                                    1) *
                                                    exam_sessions.per_page
                                            }}
                                        </td>
                                        <td>
                                            <strong class="fw-bold">{{
                                                exam_session.exam.title
                                            }}</strong>
                                            <ul class="mt-2">
                                                <li>
                                                    Kelas :
                                                    <strong class="fw-bold">{{
                                                        exam_session.exam
                                                            .classroom.title
                                                    }}</strong>
                                                </li>
                                                <li>
                                                    Pelajaran :
                                                    <strong class="fw-bold">{{
                                                        exam_session.exam.lesson
                                                            .title
                                                    }}</strong>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>{{ exam_session.title }}</td>
                                        <td class="text-center">
                                            {{
                                                exam_session.exam_groups.length
                                            }}
                                        </td>
                                        <td class="text-center">
                                            {{ exam_session.token }}
                                        </td>
                                        <!-- Display Token -->
                                        <td>{{ exam_session.start_time }}</td>
                                        <td>{{ exam_session.end_time }}</td>
                                        <td class="text-center">
                                            <Link
                                                :href="`/admin/exam_sessions/${exam_session.id}`"
                                                class="btn btn-sm btn-primary border-0 shadow me-2"
                                                type="button"
                                                ><i
                                                    class="fa fa-plus-circle"
                                                ></i
                                            ></Link>
                                            <Link
                                                :href="`/admin/exam_sessions/${exam_session.id}/edit`"
                                                class="btn btn-sm btn-info border-0 shadow me-2"
                                                type="button"
                                                ><i class="fa fa-pencil-alt"></i
                                            ></Link>
                                            <button
                                                title="Generate Token"
                                                @click.prevent="
                                                    generateToken(
                                                        exam_session.id
                                                    )
                                                "
                                                class="btn btn-sm btn-warning border-0 me-2"
                                            >
                                                <i class="fa fa-sync"></i>
                                            </button>
                                            <button
                                                @click.prevent="
                                                    destroy(exam_session.id)
                                                "
                                                class="btn btn-sm btn-danger border-0"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="exam_sessions.links" align="end" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
// Import layout and other components
import LayoutAdmin from "../../../Layouts/Admin.vue";
import Pagination from "../../../Components/Pagination.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";

export default {
    layout: LayoutAdmin,
    components: {
        Head,
        Link,
        Pagination,
    },
    props: {
        exam_sessions: Object,
    },
    setup() {
        const search = ref(
            "" || new URL(document.location).searchParams.get("q")
        );

        const handleSearch = () => {
            router.get("/admin/exam_sessions", {
                q: search.value,
            });
        };

        const destroy = (id) => {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    router.delete(`/admin/exam_sessions/${id}`);
                    Swal.fire({
                        title: "Deleted!",
                        text: "Sesi Ujian Berhasil Dihapus!.",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            });
        };

        // Method to generate token
        const generateToken = async (examSessionId) => {
            const { value: newToken } = await Swal.fire({
                title: "Generate Token Baru",
                text: "Apakah Anda ingin meng-generate token baru?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Genarate Token!",
                cancelButtonText: "Tidak, Batal!",
            });

            if (newToken) {
                try {
                    // Call your API to generate a new token
                    await router.post(
                        `/admin/exam_sessions/${examSessionId}/generate-token`
                    );
                    Swal.fire(
                        "Success!",
                        "New token has been generated.",
                        "success"
                    );
                } catch (error) {
                    Swal.fire(
                        "Error!",
                        "There was an error generating the token.",
                        "error"
                    );
                }
            }
        };

        return {
            search,
            handleSearch,
            destroy,
            generateToken,
        };
    },
};
</script>
