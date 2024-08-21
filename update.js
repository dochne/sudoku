import fs from 'fs/promises'
import sqlite3 from 'sqlite3'
import { open } from 'sqlite';
import { exists } from './utility.js'
import { promisify } from 'util'
import { exec as callbackExec } from 'child_process'
import { dirname } from 'path'

const __dirname = dirname(import.meta.url.replace('file://', ''));
const exec = promisify(callbackExec);

// console.log(__filename);
// console.log(basename(__filename));
// console.log();


(async () => {
    console.log("Running migrations");

    const db = await open({
        filename: './public/database.db',
        driver: sqlite3.Database,
    })


    await db.run(`CREATE TABLE IF NOT EXISTS "migrations" ("filename" varchar,"created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP);`)
    const allMigrations = await fs.readdir('./migrations');
    const runMigrations = [...(await db.all("SELECT filename FROM migrations"))].map((row) => row.filename);

    console.log("All", allMigrations, "run", runMigrations);
    for (const filename of allMigrations.filter((filename) => !runMigrations.includes(filename))) {
        console.log(runMigrations, "filename", filename);
        const migrationSql = (await fs.readFile('./migrations/' + filename)).toString();
        await db.exec(`BEGIN; ${migrationSql}; INSERT INTO migrations (filename) VALUES ('${filename}'); COMMIT;`)
    };
    
    
    const examples = await Promise.all((await fs.readdir('examples')).map(async filename => ({
        id: filename,
        inputFilename: __dirname + '/examples/' + filename + "/input.txt",
        output: (await fs.readFile('examples/' + filename + "/output.txt")).toString(),
    })));

    // console.log(examples);

    const folders = await fs.readdir('src');

    for (const folder of folders) {
        const metadataFilename = "src/" + folder + "/metadata.json";
        if (!(await exists(metadataFilename))) {
            continue;
        }
        
        const {language, implementations} = JSON.parse((await fs.readFile(metadataFilename)).toString());
        console.log(`Processing ${language}`)
    
        for (const [implementation, info] of Object.entries(implementations)) {
            console.log(`  Implementaton ${implementation}`)
            await db.run(
                'INSERT INTO implementations (language, name) VALUES (?, ?) ON CONFLICT (language, name) DO NOTHING',
                language,
                implementation,
            )

            const implementationRow = await db.get(
                'SELECT * FROM implementations WHERE language=? AND name=?',
                language,
                implementation
            );

            // We'll need to do a check on force
            const lastExecution = await db.all(
                'SELECT count(*) as total FROM executions WHERE implementation_id=? ORDER BY created_at DESC',
                implementationRow.id
            );

            if (lastExecution[0].total !== examples.length) {
                console.log(`  Attempting Execution - ${language} ${implementation}`);
                const directory = __dirname + `/src/${folder}/` + (info['dir'] ?? '');

                if (info['build'] !== undefined) {
                    try{
                        await exec(info['build'], {
                            cwd: directory
                        })    
                    } catch (err) {
                        console.error("    Unable to build project", err);
                        continue;
                    }
                }
                
                for (const example of examples) {
                    console.log("Executing", example.id);
                    try{
                        const before = process.hrtime.bigint();
// grid.solve();

// const duration = process.hrtime.bigint() - start;

//                         const before = Date.now();
                        const { error, stdout, stderr } = await exec(`${info['run']} ${example.inputFilename}`, {
                            cwd: directory
                        });
                        // const nodeDuration = (Date.now() - before);
                        const nodeDuration = process.hrtime.bigint() - before;
                        const trimmed = stdout.trim();

                        let output = '';
                        let time = null;
                        let meta = {};

                        try{
                            const parsed = JSON.parse(trimmed.replaceAll("\n", ""));
                            output = (parsed['output'] || output).trim();
                            time = parsed['time'] || time
                            meta = parsed['meta'] || meta;
                            // console.log(parsed);
                        } catch (err) {
                            // console.log(trimmed.replaceAll("\n", ""));
                            console.log(err);
                        }

                        // console.log("Parsed", example.output, output, time);

                        // console.log("Duration", JSON.parse(stdout.trim()));
                        await db.run("DELETE FROM executions WHERE implementation_id=? AND example_id=?", [implementationRow['id'], example['id']]);

                        // console.log("Input1", output.replaceAll(" ", "").replaceAll("\n", ""), "Input2", example.output.replaceAll(" ", "").replaceAll("\n", ""));

                        // console.log("Foo");
                        await db.run(
                            `INSERT INTO executions
                                (implementation_id, example_id, self_duration, node_duration, meta, result)
                                VALUES (?, ?, ?, ?, ?, ?)`,
                            implementationRow['id'],
                            example['id'],
                            parseInt(time, 10),
                            parseInt(Number(nodeDuration) / 1_000, 10),
                            JSON.stringify(meta),
                            (output.replaceAll(" ", "").replaceAll("\n", "") === example.output.replaceAll(" ", "").replaceAll("\n", "")) ? 1 : 0
                        );

                        // let same = output.replaceAll(" ", "").replaceAll("\n", "") === example.output.replaceAll(" ", "").replaceAll("\n", "") ? 1 : 0

                        // console.log("Compare", output.replaceAll(' ', ''), "Output2", example.output.replaceAll(' ', ''), same);
                        console.log("Execution complete!");
                    } catch (err) {
                        console.log("Err", err)
                        continue;
                    }   
                }
            }

            // console.log(lastExecution);





            // examples.forEach()
            // if (value)
            // console.log("Value", value);




            // console.log(result);
            // const foo = await db.run(`INSERT OR     `)

            // const implementation = 
            // console.log(language, implementations);
        };
        
        // const metadata = JSON.parse("")
        // implementations.push([])
        // console.log(language);
    };

})();


// console.log(files);